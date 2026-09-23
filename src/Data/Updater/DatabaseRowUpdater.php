<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Updater;

use Contao\CoreBundle\Cache\CacheTagManager;
use Contao\CoreBundle\DataContainer\VirtualFieldsHandler;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\Security\DataContainer\ReadAction;
use Contao\CoreBundle\Security\DataContainer\UpdateAction;
use Contao\DataContainer;
use Contao\DC_File;
use Contao\StringUtil;
use Contao\Versions;
use Contao\Widget;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Exception\AccessDenied;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Override;
use Symfony\Bundle\SecurityBundle\Security;

use function array_diff_key;
use function array_filter;
use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function implode;
use function is_array;
use function serialize;
use function sprintf;
use function time;

/**
 * Updates a database row the same way Contao's DC_Table driver does it when toggling or saving a record.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
final class DatabaseRowUpdater implements Updater
{
    private const MODE_PARENT = 4;

    private const MODE_TREE = 5;

    public function __construct(
        private readonly Security $security,
        private readonly Connection $connection,
        private readonly DcaManager $dcaManager,
        private readonly Invoker $invoker,
        private readonly VirtualFieldsHandler $virtualFieldsHandler,
        private readonly CacheTagManager $cacheTagManager,
    ) {
    }

    /**
     * Update a data record.
     *
     * @param string              $dataContainerName Data container name.
     * @param int|string          $recordId          Data record id.
     * @param array<string,mixed> $data              Raw values of the row which should be changed.
     * @param mixed               $context           Context, usually the data container driver.
     *
     * @return array<string,mixed> The saved values.
     *
     * @throws AccessDenied When the table is not editable, the record doesn't exist or the user has no access.
     * @throws InvalidArgumentException When a value of a unique field already exists.
     */
    #[Override]
    public function update(string $dataContainerName, int|string $recordId, array $data, mixed $context): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainerName);

        if ($definition->get(['config', 'notEditable'])) {
            throw new AccessDenied(sprintf('Table "%s" is not editable.', $dataContainerName));
        }

        $this->guardUserHasFieldAccess($definition, $recordId, $data);

        $record        = $this->loadRecord($definition, $recordId);
        $currentRecord = $this->virtualFieldsHandler->expandFields($record, $dataContainerName);
        $virtualValues = array_diff_key($currentRecord, $record);
        $versions      = $this->initializeVersions($definition, $recordId);
        $values        = [];

        foreach ($data as $field => $value) {
            $value = $this->prepareValue($definition, $currentRecord, $field, $value, $context);
            if (! $this->shouldSave($definition, $currentRecord, $field, $value)) {
                continue;
            }

            $values[$field] = $this->convertEmptyValue($definition, $field, $value);
        }

        if ($values !== []) {
            $values['tstamp'] = time();
            $values           = $this->invokeBeforeSubmitCallbacks($definition, $values, $context);
        }

        $this->guardUserCanUpdate($definition, $currentRecord, $values);

        if ($values !== [] && $this->persist($definition, $currentRecord, $virtualValues, $recordId, $values)) {
            DataContainer::clearCurrentRecordCache($recordId, $dataContainerName);
            $this->invalidateCacheTags($definition, $currentRecord, $recordId, $context);

            if ($versions && $this->isVersionized($definition, $values)) {
                $versions->create();
            }
        }

        $this->invokeSubmitCallbacks($definition, $context);

        return $values;
    }

    /**
     * Check if the user has access to a field. Only excluded fields require an explicit permission.
     *
     * @param string $dataContainerName Data container name.
     * @param string $columnName        Column name.
     */
    #[Override]
    public function hasUserAccess(string $dataContainerName, string $columnName): bool
    {
        $definition = $this->dcaManager->getDefinition($dataContainerName);

        if (! $this->isFieldExcluded($definition, $columnName)) {
            return true;
        }

        return $this->security->isGranted(
            ContaoCorePermissions::USER_CAN_EDIT_FIELD_OF_TABLE,
            $dataContainerName . '::' . $columnName,
        );
    }

    /**
     * Mirrors DataContainer::isFieldExcluded() based on the given definition.
     */
    private function isFieldExcluded(Definition $definition, string $field): bool
    {
        if ($definition->get(['config', 'dataContainer']) === DC_File::class) {
            return false;
        }

        $exclude = $definition->get(['fields', $field, 'exclude']);
        if ($exclude !== null) {
            return (bool) $exclude;
        }

        return ! empty($definition->get(['fields', $field, 'inputType']))
            || ! empty($definition->get(['fields', $field, 'input_field_callback']));
    }

    /** @param array<string,mixed> $data */
    private function guardUserHasFieldAccess(Definition $definition, int|string $recordId, array $data): void
    {
        foreach (array_keys($data) as $field) {
            if (! $this->hasUserAccess($definition->getName(), $field)) {
                throw new AccessDenied(
                    sprintf(
                        'Not enough permissions to edit field "%s.%s" of record ID "%s".',
                        $definition->getName(),
                        $field,
                        $recordId,
                    ),
                );
            }
        }
    }

    /** @return array<string,mixed> */
    private function loadRecord(Definition $definition, int|string $recordId): array
    {
        $record = $this->connection->fetchAssociative(
            sprintf('SELECT * FROM %s WHERE id=?', $this->quote($definition->getName())),
            [$recordId],
        );

        if ($record === false) {
            throw new AccessDenied(sprintf('Cannot load record "%s.id=%s".', $definition->getName(), $recordId));
        }

        if (
            ! $this->security->isGranted(
                ContaoCorePermissions::DC_PREFIX . $definition->getName(),
                new ReadAction($definition->getName(), $record),
            )
        ) {
            throw new AccessDenied(
                sprintf('Not enough permissions to read record "%s.id=%s".', $definition->getName(), $recordId),
            );
        }

        return $record;
    }

    private function quote(string $identifier): string
    {
        return $this->connection->getDatabasePlatform()->quoteSingleIdentifier($identifier);
    }

    private function initializeVersions(Definition $definition, int|string $recordId): Versions|null
    {
        if (! $definition->get(['config', 'enableVersioning'])) {
            return null;
        }

        $versions = new Versions($definition->getName(), (int) $recordId);
        $versions->initialize();

        return $versions;
    }

    /**
     * Convert csv values, trigger the save callbacks and check unique values.
     *
     * @param array<string,mixed> $currentRecord
     */
    private function prepareValue(
        Definition $definition,
        array $currentRecord,
        string $field,
        mixed $value,
        mixed $context,
    ): mixed {
        $csv = $definition->get(['fields', $field, 'eval', 'csv']);
        if ($csv !== null && $definition->get(['fields', $field, 'eval', 'multiple'])) {
            $value = implode((string) $csv, StringUtil::deserialize($value, true));
        }

        $callbacks = $definition->get(['fields', $field, 'save_callback']);
        if (is_array($callbacks) && $callbacks !== []) {
            $value = $this->invoker->invokeAll($callbacks, [$value, $context], 0);
        }

        if (
            $definition->get(['fields', $field, 'eval', 'unique'])
            && (is_array($value) || (string) $value !== '')
            && ! $this->isUniqueValue($definition, $field, $value, $currentRecord['id'])
        ) {
            throw new InvalidArgumentException(
                sprintf('Value of field "%s.%s" has to be unique.', $definition->getName(), $field),
            );
        }

        return $value;
    }

    private function isUniqueValue(Definition $definition, string $field, mixed $value, mixed $recordId): bool
    {
        $result = $this->connection->fetchOne(
            sprintf(
                'SELECT COUNT(*) FROM %s WHERE %s=? AND id!=?',
                $this->quote($definition->getName()),
                $this->quote($field),
            ),
            [is_array($value) ? serialize($value) : $value, $recordId],
        );

        return (int) $result === 0;
    }

    /**
     * Mirrors the conditions of DC_Table::save(): empty values are skipped for doNotSaveEmpty fields and unchanged
     * values are only saved for alwaysSave fields.
     *
     * @param array<string,mixed> $currentRecord
     */
    private function shouldSave(Definition $definition, array $currentRecord, string $field, mixed $value): bool
    {
        if (
            ! is_array($value)
            && (string) $value === ''
            && $definition->get(['fields', $field, 'eval', 'doNotSaveEmpty'])
        ) {
            return false;
        }

        return ($currentRecord[$field] ?? null) !== $value
            || (bool) $definition->get(['fields', $field, 'eval', 'alwaysSave']);
    }

    private function convertEmptyValue(Definition $definition, string $field, mixed $value): mixed
    {
        if (is_array($value) || (string) $value !== '') {
            return $value;
        }

        return Widget::getEmptyValueByFieldType($definition->get(['fields', $field, 'sql'], []));
    }

    /**
     * @param array<string,mixed> $values
     *
     * @return array<string,mixed>
     */
    private function invokeBeforeSubmitCallbacks(Definition $definition, array $values, mixed $context): array
    {
        foreach ((array) $definition->get(['config', 'onbeforesubmit_callback'], []) as $callback) {
            $values = $this->invoker->invoke($callback, [$values, $context]);

            if (! is_array($values)) {
                throw new InvalidArgumentException('The onbeforesubmit_callback must return the values!');
            }
        }

        return $values;
    }

    /**
     * @param array<string,mixed> $currentRecord
     * @param array<string,mixed> $values
     */
    private function guardUserCanUpdate(Definition $definition, array $currentRecord, array $values): void
    {
        $granted = $this->security->isGranted(
            ContaoCorePermissions::DC_PREFIX . $definition->getName(),
            new UpdateAction($definition->getName(), $currentRecord, $values),
        );

        if (! $granted) {
            throw new AccessDenied(
                sprintf(
                    'Not enough permissions to update record "%s.id=%s".',
                    $definition->getName(),
                    $currentRecord['id'],
                ),
            );
        }
    }

    /**
     * Persist the values. Returns true if the record was changed.
     *
     * @param array<string,mixed> $currentRecord
     * @param array<string,mixed> $virtualValues Values of the virtual fields of the current record.
     * @param array<string,mixed> $values
     */
    private function persist(
        Definition $definition,
        array $currentRecord,
        array $virtualValues,
        int|string $recordId,
        array $values,
    ): bool {
        $tableName = $definition->getName();

        // Merge the virtual fields of the current record, so that the storage fields can be combined.
        $values = $this->virtualFieldsHandler->combineFields(array_merge($virtualValues, $values), $tableName);

        $builder = $this->connection->createQueryBuilder()
            ->update($this->quote($tableName))
            ->where('id = :id')
            ->setParameter('id', $recordId);

        foreach ($values as $field => $value) {
            $field = (string) $field;
            $type  = $definition->get(['fields', $field, 'sql', 'type']);

            if ($value && $definition->get(['fields', $field, 'eval', 'fallback'])) {
                $this->resetFallbackField($definition, $currentRecord, $field);
            }

            if (is_array($value) && $type !== Types::JSON) {
                $value = serialize($value);
            }

            $builder
                ->set($this->quote($field), ':' . $field)
                ->setParameter($field, $value, $type);
        }

        return $builder->executeStatement() > 0;
    }

    /**
     * If the field is a fallback field, empty all other columns (see contao/core#6498).
     *
     * @param array<string,mixed> $currentRecord
     */
    private function resetFallbackField(Definition $definition, array $currentRecord, string $field): void
    {
        $emptyValue = Widget::getEmptyValueByFieldType($definition->get(['fields', $field, 'sql'], []));
        $type       = $definition->get(['fields', $field, 'sql', 'type']);
        $builder    = $this->connection->createQueryBuilder()
            ->update($this->quote($definition->getName()))
            ->set($this->quote($field), ':value')
            ->setParameter('value', $emptyValue, $type);

        if ((int) $definition->get(['list', 'sorting', 'mode']) === self::MODE_PARENT) {
            $builder->where('pid = :pid')->setParameter('pid', $currentRecord['pid'] ?? null);
        }

        $builder->executeStatement();
    }

    /**
     * Mirrors DataContainer::invalidateCacheTags() and DataContainer::addPtableTags().
     *
     * @param array<string,mixed> $currentRecord
     */
    private function invalidateCacheTags(
        Definition $definition,
        array $currentRecord,
        int|string $recordId,
        mixed $context,
    ): void {
        $tableName = $definition->getName();
        $tags      = ['contao.db.' . $tableName . '.' . $recordId];
        $ptable    = (int) $definition->get(['list', 'sorting', 'mode']) === self::MODE_TREE
            ? $tableName
            : $definition->get(['config', 'ptable']);

        if (! $ptable || empty($currentRecord['pid'])) {
            $tags[] = 'contao.db.' . $tableName;
        } else {
            $tags[] = 'contao.db.' . $ptable . '.' . $currentRecord['pid'];
        }

        if ($context instanceof DataContainer) {
            foreach ((array) $definition->get(['config', 'oninvalidate_cache_tags_callback'], []) as $callback) {
                $tags = $this->invoker->invoke($callback, [$context, $tags]);
            }
        }

        $this->cacheTagManager->invalidateTags(array_values(array_filter(array_unique($tags))));
    }

    /** @param array<string,mixed> $values */
    private function isVersionized(Definition $definition, array $values): bool
    {
        foreach (array_keys($values) as $field) {
            if ($definition->get(['fields', $field, 'eval', 'versionize']) !== false) {
                return true;
            }
        }

        return false;
    }

    private function invokeSubmitCallbacks(Definition $definition, mixed $context): void
    {
        foreach ((array) $definition->get(['config', 'onsubmit_callback'], []) as $callback) {
            $this->invoker->invoke($callback, [$context]);
        }
    }
}
