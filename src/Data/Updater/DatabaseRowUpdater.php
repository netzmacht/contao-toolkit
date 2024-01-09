<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Updater;

use Contao\BackendUser;
use Contao\Versions;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Exception\AccessDenied;
use Symfony\Component\Security\Core\Security;

use function array_keys;
use function is_array;
use function sprintf;
use function time;

final class DatabaseRowUpdater implements Updater
{
    /**
     * @param Connection $connection Database connection.
     * @param DcaManager $dcaManager Data container manager.
     * @param Invoker    $invoker    Callback invoker.
     */
    public function __construct(
        private readonly Security $security,
        private readonly Connection $connection,
        private readonly DcaManager $dcaManager,
        private readonly Invoker $invoker,
    ) {
    }

    /**
     * Toggle the state.
     *
     * @param string              $dataContainerName Data container name.
     * @param int|string          $recordId          Data record id.
     * @param array<string,mixed> $data              Data of the row which should be changed.
     * @param mixed               $context           Context, usually the data container driver.
     *
     * @return array<string,mixed>
     */
    public function update(string $dataContainerName, int|string $recordId, array $data, mixed $context): array
    {
        $this->guardUserHasAccess($dataContainerName, $recordId, $data);

        $definition = $this->dcaManager->getDefinition($dataContainerName);
        $versions   = $this->initializeVersions($definition, $recordId);
        $data       = $this->executeSaveCallbacks($definition, $data, $context);

        $this->save($definition, $recordId, $data);

        if ($versions) {
            $versions->create();
        }

        return $data;
    }

    /**
     * Check if user has access.
     *
     * @param string $dataContainerName Data container name.
     * @param string $columnName        Column name.
     */
    public function hasUserAccess(string $dataContainerName, string $columnName): bool
    {
        $user = $this->security->getUser();

        if (! $user instanceof BackendUser) {
            return false;
        }

        return $user->hasAccess($dataContainerName . '::' . $columnName, 'alexf');
    }

    /**
     * Guard that user has access.
     *
     * @param string              $tableName Data container name.
     * @param int|string          $recordId  Record id.
     * @param array<string,mixed> $data      Data row.
     *
     * @throws AccessDenied When user has no access.
     */
    private function guardUserHasAccess(string $tableName, int|string $recordId, array $data): void
    {
        foreach (array_keys($data) as $columnName) {
            if (! $this->hasUserAccess($tableName, $columnName)) {
                throw new AccessDenied(
                    sprintf('Not enough permission to toggle record ID "%s::%s"', $tableName, $recordId),
                );
            }
        }
    }

    /**
     * Initialize versions if data container support versioning.
     *
     * @param Definition $definition Data container definition.
     * @param int|string $recordId   Record id.
     */
    private function initializeVersions(Definition $definition, int|string $recordId): Versions|null
    {
        if ($definition->get(['config', 'enableVersioning'])) {
            $versions = new Versions($definition->getName(), (int) $recordId);
            $versions->initialize();

            return $versions;
        }

        return null;
    }

    /**
     * Execute save callbacks.
     *
     * @param Definition          $definition Data container definition.
     * @param array<string,mixed> $data       Data row.
     * @param mixed               $context    Context, usually the data container driver.
     *
     * @return array<string,mixed>
     */
    private function executeSaveCallbacks(Definition $definition, array $data, mixed $context): array
    {
        foreach ($data as $column => $value) {
            $callbacks = $definition->get(['fields', $column, 'save_callback']);

            if (! is_array($callbacks)) {
                continue;
            }

            $data[$column] = $this->invoker->invokeAll($callbacks, [$value, $context], 0);
        }

        return $data;
    }

    /**
     * Save new state in database.
     *
     * @param Definition          $definition Data container definition.
     * @param int|string          $recordId   Data record id.
     * @param array<string,mixed> $data       Change data set.
     */
    private function save(Definition $definition, int|string $recordId, array $data): void
    {
        $builder = $this->connection->createQueryBuilder()
            ->update($definition->getName())
            ->where('id = :id')
            ->setParameter('id', $recordId);

        foreach ($data as $column => $value) {
            // Filter empty values which should not be saved.
            if ($value === '') {
                if (
                    $definition->get(['fields', $column, 'eval', 'alwaysSave'], false)
                    && $definition->get(['fields', $column, 'eval', 'doNotSaveEmpty'], false)
                ) {
                    continue;
                }
            }

            $builder->set($column, ':' . $column);
            $builder->setParameter($column, (string) $value);
        }

        // Add tstamp if field exists.
        $columns = $this->connection->createSchemaManager()->listTableColumns($definition->getName());
        if (empty($data['tstamp']) && isset($columns['tstamp'])) {
            $builder->set('tstamp', (string) time());
        }

        // Store the data.
        $builder->executeStatement();
    }
}
