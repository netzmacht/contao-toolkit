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

namespace Netzmacht\Contao\Toolkit\DependencyInjection\Listener;

/**
 * Class MergerHookListenerListener
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\Listener
 */
class MergeHookListenersListener
{
    /**
     * Hook listeners.
     *
     * @var array
     */
    private $hookListeners;

    /**
     * MergerHookListenerListener constructor.
     *
     * @param array $hookListeners Hook listeners.
     */
    public function __construct(array $hookListeners = [])
    {
        $this->hookListeners = $hookListeners;
    }

    /**
     * Initialize the system
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function onInitializeSystem(): void
    {
        foreach ($this->hookListeners as $hookName => $priorities) {
            $hooks = [];

            foreach ($priorities as $priority => $listeners) {
                if ($priority <= 0 && isset($GLOBALS['TL_HOOKS'][$hookName])) {
                    $hooks = array_merge($hooks, $GLOBALS['TL_HOOKS'][$hookName]);
                    unset($GLOBALS['TL_HOOKS'][$hookName]);
                }

                $hooks = array_merge($hooks, $listeners);
            }

            // Add legacy hook if only hook listeners with high priority are defined
            if (isset($GLOBALS['TL_HOOKS'][$hookName])) {
                $hooks = array_merge($hooks, $GLOBALS['TL_HOOKS'][$hookName]);
            }

            $GLOBALS['TL_HOOKS'][$hookName] = $hooks;
        }
    }
}
