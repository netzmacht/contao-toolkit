<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Updater;

use Contao\BackendUser;
use Contao\Versions;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Exception\AccessDenied;
use function is_array;

/**
 * Class DatabaseRowUpdater.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Updater
 */
final class DatabaseRowUpdater implements Updater
{
    /**
     * Contao backend user..
     *
     * @var BackendUser
     */
    private $backendUser;

    /**
     * The database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Callback invoker.
     *
     * @var Invoker
     */
    private $invoker;

    /**
     * Data container manager.
     *
     * @var Manager
     */
    private $dcaManager;

    /**
     * DatabaseRowUpdater constructor.
     *
     * @param BackendUser $backendUser Backend user.
     * @param Connection  $connection  Database connection.
     * @param Manager     $dcaManager  Data container manager.
     * @param Invoker     $invoker     Callback invoker.
     */
    public function __construct(
        BackendUser $backendUser,
        Connection $connection,
        Manager $dcaManager,
        Invoker $invoker
    ) {
        $this->backendUser = $backendUser;
        $this->connection  = $connection;
        $this->invoker     = $invoker;
        $this->dcaManager  = $dcaManager;
    }

    /**
     * Toggle the state.
     *
     * @param string $tableName Data container name.
     * @param int    $recordId  Data record id.
     * @param array  $data      Data of the row which should be changed.
     * @param mixed  $context   Context, usually the data container driver.
     *
     * @return array
     */
    public function update($tableName, $recordId, array $data, $context): array
    {
        $this->guardUserHasAccess($tableName, $recordId, $data);

        $definition = $this->dcaManager->getDefinition($tableName);
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
     * @param string $tableName  Data container name.
     * @param string $columnName Column name.
     *
     * @return bool
     */
    public function hasUserAccess($tableName, $columnName): bool
    {
        if (TL_MODE !== 'BE') {
            return false;
        }

        return $this->backendUser->hasAccess($tableName . '::' . $columnName, 'alexf');
    }

    /**
     * Guard that user has access.
     *
     * @param string $tableName Data container name.
     * @param int    $recordId  Record id.
     * @param array  $data      Data row.
     *
     * @return void
     * @throws AccessDenied When user has no access.
     */
    private function guardUserHasAccess($tableName, $recordId, array $data): void
    {
        foreach (array_keys($data) as $columnName) {
            if (!$this->hasUserAccess($tableName, $columnName)) {
                throw new AccessDenied(
                    sprintf('Not enough permission to toggle record ID "%s::%s"', $tableName, $recordId)
                );
            }
        }
    }

    /**
     * Initialize versions if data container support versioning.
     *
     * @param Definition $definition Data container definition.
     * @param int        $recordId   Record id.
     *
     * @return Versions|null
     */
    private function initializeVersions(Definition $definition, $recordId): ?Versions
    {
        if ($definition->get(['config', 'enableVersioning'])) {
            $versions = new Versions($definition->getName(), $recordId);
            $versions->initialize();

            return $versions;
        }

        return null;
    }

    /**
     * Execute save callbacks.
     *
     * @param Definition $definition Data container definition.
     * @param array      $data       Data row.
     * @param mixed      $context    Context, usually the data container driver.
     *
     * @return array
     */
    private function executeSaveCallbacks(Definition $definition, array $data, $context): array
    {
        foreach ($data as $column => $value) {
            $callbacks = $definition->get(['fields', $column, 'save_callback']);

            if (is_array($callbacks)) {
                $data[$column] = $this->invoker->invokeAll($callbacks, [$value, $context], 0);
            }
        }

        return $data;
    }

    /**
     * Save new state in database.
     *
     * @param Definition $definition Data container definition.
     * @param int        $recordId   Data record id.
     * @param array      $data       Change data set.
     *
     * @return void
     */
    private function save(Definition $definition, $recordId, array $data): void
    {
        $builder = $this->connection->createQueryBuilder()
            ->update($definition->getName())
            ->where('id = :id')
            ->setParameter('id', $recordId);

        foreach ($data as $column => $value) {
            // Filter empty values which should not be saved.
            if ($value == '') {
                if ($definition->get(['fields', $column, 'eval', 'alwaysSave'], false)
                    && $definition->get(['fields', $column, 'eval', 'doNotSaveEmpty'], false)
                ) {
                    continue;
                }
            }

            $builder->set($column, ':' . $column);
            $builder->setParameter($column, (string) $value);
        }

        // Add tstamp if field exists.
        $columns = $this->connection->getSchemaManager()->listTableColumns($definition->getName());
        if (empty($data['tstamp']) && isset($columns['tstamp'])) {
            $builder->set('tstamp', time());
        }

        // Store the data.
        $builder->execute();
    }
}
