<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Button\Callback;

/**
 * StateButtonCallbackFactory provides a static factory method for the state button callback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Button\Callback
 */
trait StateButtonCallbackFactory
{
    /**
     * State button callback instances.
     *
     * @var StateButtonCallback[]
     */
    protected static $stateButtonCallbacks = [];

    /**
     * Get the state button callback for a button.
     *
     * @param string $buttonName Button name.
     *
     * @return StateButtonCallback
     */
    protected static function getStateButtonCallback($buttonName)
    {
        if (!isset(static::$stateButtonCallbacks[$buttonName])) {
            static::$stateButtonCallbacks[$buttonName] = new StateButtonCallback(
                static::getServiceContainer()->getUser(),
                static::getServiceContainer()->getInput(),
                static::getServiceContainer()->getDatabaseConnection(),
                static::getDefinition(),
                $buttonName
            );
        }

        return static::$stateButtonCallbacks[$buttonName];
    }

    /**
     * Create the state button callback.
     *
     * @param string $buttonName Button name.
     *
     * @return callable
     */
    public static function stateButtonCallback($buttonName)
    {
        return function () use ($buttonName) {
            return call_user_func_array(static::getStateButtonCallback($buttonName), func_get_args());
        };
    }
}
