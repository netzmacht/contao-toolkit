<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\State;

use BackendUser;
use Database;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use User;
use Versions;
use Netzmacht\Contao\Toolkit\Dca\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Data\Exception\AccessDenied;

/**
 * Class StateToggler.
 *
 * @package Netzmacht\Contao\Toolkit\Data\State
 */
final class StateToggle
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
     * StateToggler constructor.
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
     * @param string $tableName  Data container name.
     * @param string $columnName Column name.
     * @param int    $recordId   Data record id.
     * @param mixed  $newState   New state value.
     * @param mixed  $context    Context, usually the data container driver.
     *
     * @return mixed
     * @throws AccessDenied When user has no access.
     */
    public function toggle($tableName, $columnName, $recordId, $newState, $context)
    {
        $this->guardUserHasAccess($tableName, $columnName, $recordId);

        $definition = $this->dcaManager->getDefinition($tableName);
        $versions   = $this->initializeVersions($definition, $recordId);
        $newState   = $this->executeSaveCallbacks($definition, $columnName, $newState, $context);

        $this->saveValue($tableName, $columnName, $recordId, $newState);

        if ($versions) {
            $versions->create();
        }

        return $newState;
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
     * @param string $tableName  Data container name.
     * @param string $columnName Column name.
     * @param int    $recordId   Record id.
     *
     * @return void
     * @throws AccessDenied When user has no access.
     */
    private function guardUserHasAccess($tableName, $columnName, $recordId)
    {
        if (!$this->hasUserAccess($tableName, $columnName)) {
            throw new AccessDenied(
                sprintf('Not enough permission to toggle record ID "%s::%s"', $tableName, $recordId)
            );
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
     * @param string     $columnName State column name.
     * @param mixed      $newState   New state value.
     * @param mixed      $context    Context, usually the data container driver.
     *
     * @return mixed
     */
    private function executeSaveCallbacks(Definition $definition, $columnName, $newState, $context)
    {
        $callbacks = $definition->get(['fields', $columnName, 'save_callback']);

        if (is_array($callbacks)) {
            $newState = $this->invoker->invokeAll($callbacks, [$newState, $context], 0);
        }

        return $newState;
    }

    /**
     * Save new state in database.
     *
     * @param string $tableName  Data container name.
     * @param string $columnName Column name.
     * @param int    $recordId   Data record id.
     * @param mixed  $newState   New state value.
     *
     * @return void
     */
    private function saveValue($tableName, $columnName, $recordId, $newState)
    {
        $this->database
            ->prepare(sprintf('UPDATE %s %s WHERE id=?', $tableName, '%s'))
            ->set(array('tstamp' => time(), $columnName => $newState ? '1' : ''))
            ->execute($recordId);
    }
}
