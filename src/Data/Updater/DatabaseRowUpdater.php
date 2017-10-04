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

namespace Netzmacht\Contao\Toolkit\Data\Updater;

use Contao\BackendUser;
use Contao\Database;
use Contao\User;
use Contao\Versions;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\Dca\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Data\Exception\AccessDenied;

/**
 * Class DatabaseRowUpdater.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Updater
 */
final class DatabaseRowUpdater implements Updater
{
    /**
     * Contao user.
     *
     * @var User
     */
    private $user;

    /**
     * The database connection.
     *
     * @var Database
     */
    private $database;

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
     * @param User     $user       User object.
     * @param Database $database   Database connection.
     * @param Manager  $dcaManager Data container manager.
     * @param Invoker  $invoker    Callback invoker.
     */
    public function __construct(User $user, Database $database, Manager $dcaManager, Invoker $invoker)
    {
        $this->user       = $user;
        $this->database   = $database;
        $this->invoker    = $invoker;
        $this->dcaManager = $dcaManager;
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
    public function update($tableName, $recordId, array $data, $context)
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
    public function hasUserAccess($tableName, $columnName)
    {
        if ($this->user instanceof BackendUser) {
            return $this->user->hasAccess($tableName . '::' . $columnName, 'alexf');
        }

        return false;
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
    private function guardUserHasAccess($tableName, $recordId, array $data)
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
    private function initializeVersions(Definition $definition, $recordId)
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
    private function executeSaveCallbacks(Definition $definition, array $data, $context)
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
    private function save(Definition $definition, $recordId, array $data)
    {
        // Filter empty values which should not be saved.
        foreach ($data as $column => $value) {
            if ($value == '') {
                if ($definition->get(['fields', $column, 'eval', 'alwaysSave'], false)
                    && $definition->get(['fields', $column, 'eval', 'doNotSaveEmpty'], false)
                ) {
                    unset($data[$column]);
                }
            }
        }

        // Add tstamp if field exists.
        if (empty($data['tstamp'] && $this->database->fieldExists('tstamp', $definition->getName()))) {
            $data['tstamp'] = time();
        }

        // Store the data.
        $this->database
            ->prepare(sprintf('UPDATE %s %s WHERE id=?', $definition->getName(), '%s'))
            ->set($data)
            ->execute($recordId);
    }
}
