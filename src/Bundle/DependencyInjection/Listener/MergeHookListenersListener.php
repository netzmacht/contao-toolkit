<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Listener;

use Netzmacht\Contao\Toolkit\Callback\Invoker;
use function array_merge;

/**
 * Class MergerHookListenerListener.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\Listener
 */
final class MergeHookListenersListener
{
    /**
     * Hook listeners.
     *
     * @var array
     */
    private $hookListeners;

    /**
     * Callback invoker.
     *
     * @var Invoker
     */
    private $invoker;

    /**
     * MergerHookListenerListener constructor.
     *
     * @param Invoker $invoker       Callback invoker.
     * @param array   $hookListeners Hook listeners.
     */
    public function __construct(Invoker $invoker, array $hookListeners = [])
    {
        $this->hookListeners = $hookListeners;
        $this->invoker       = $invoker;
    }

    /**
     * Initialize the system.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function onInitializeSystem(): void
    {
        // Hook initializeSystem listeners can't be merged so just invoke them.
        if (isset($this->hookListeners['initializeSystem'])) {
            foreach ($this->hookListeners['initializeSystem'] as $listeners) {
                $this->invoker->invokeAll($listeners);
            }

            unset($this->hookListeners['initializeSystem']);
        }

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
