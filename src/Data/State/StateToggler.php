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

use Contao\BackendUser;
use Contao\Database;
use Contao\User;
use Contao\Versions;
use Netzmacht\Contao\Toolkit\Dca\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Exception\AccessDeniedException;

/**
 * Class StateToggler.
 *
 * @package Netzmacht\Contao\Toolkit\Data\State
 */
class StateToggler
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
     * Data container definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * State column.
     *
     * @var string
     */
    private $stateColumn;

    /**
     * Callback invoker.
     *
     * @var Invoker
     */
    private $invoker;

    /**
     * StateToggler constructor.
     *
     * @param User       $user        Contao user object.
     * @param Database   $database    Database connection.
     * @param Definition $definition  Data container definition.
     * @param Invoker    $invoker     Callback invoker.
     * @param string     $stateColumn State column.
     */
    public function __construct(User $user, Database $database, Definition $definition, Invoker $invoker, $stateColumn)
    {
        $this->user        = $user;
        $this->database    = $database;
        $this->definition  = $definition;
        $this->invoker     = $invoker;
        $this->stateColumn = $stateColumn;
    }

    /**
     * Toggle the state.
     *
     * @param int   $recordId Data record id.
     * @param mixed $newState New state value.
     * @param mixed $context  Context, usually the data container driver.
     *
     * @return mixed
     * @throws AccessDeniedException When user has no access.
     */
    public function toggle($recordId, $newState, $context)
    {
        $this->guardUserHasAccess($recordId);

        $versions = $this->initializeVersions($recordId);
        $newState = $this->executeSaveCallbacks($newState, $context);

        $this->saveValue($recordId, $newState);

        if ($versions) {
            $versions->create();
        }

        return $newState;
    }

    /**
     * Check if user has access.
     *
     * @return bool
     */
    public function hasUserAccess()
    {
        if ($this->user instanceof BackendUser) {
            return $this->user->hasAccess($this->definition->getName() . '::' . $this->stateColumn, 'alexf');
        }

        return false;
    }

    /**
     * Guard that user has access.
     *
     * @param int $recordId Record id.
     *
     * @return void
     * @throws AccessDeniedException When user has no access.
     */
    private function guardUserHasAccess($recordId)
    {
        if (!$this->hasUserAccess()) {
            throw new AccessDeniedException(
                sprintf('Not enough permission to show/shide record ID "%s"', $recordId)
            );
        }
    }

    /**
     * Initialize versions if data container support versioning.
     *
     * @param int $recordId Record id.
     *
     * @return Versions|null
     */
    private function initializeVersions($recordId)
    {
        if ($this->definition->get(['config', 'enableVersioning'])) {
            $versions = new Versions($this->definition->getName(), $recordId);
            $versions->initialize();

            return $versions;
        }

        return null;
    }

    /**
     * Execute save callbacks.
     *
     * @param mixed $newState New state value.
     * @param mixed $context  Context, usually the data container driver.
     *
     * @return mixed
     */
    private function executeSaveCallbacks($newState, $context)
    {
        $callbacks = $this->definition->get(['fields', $this->stateColumn, 'save_callback']);

        if (is_array($callbacks)) {
            $newState = $this->invoker->invokeAll($callbacks, [$newState, $context], 0);
        }

        return $newState;
    }

    /**
     * Save new state in database.
     *
     * @param int   $recordId Data record id.
     * @param mixed $newState New state value.
     *
     * @return void
     */
    private function saveValue($recordId, $newState)
    {
        $this->database
            ->prepare(sprintf('UPDATE %s %s WHERE id=?', $this->definition->getName(), '%s'))
            ->set(array('tstamp' => time(), $this->stateColumn => $newState ? '1' : ''))
            ->execute($recordId);
    }
}
