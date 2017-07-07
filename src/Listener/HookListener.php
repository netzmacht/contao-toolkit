<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Listener;

use Netzmacht\Contao\Toolkit\Boot\Event\InitializeSystemEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * Boot the environment.
 *
 * @package Netzmacht\Contao\Toolkit
 */
final class HookListener
{
    /**
     * Event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * HookListener constructor.
     *
     * @param EventDispatcher $eventDispatcher Event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Initialize.
     *
     * This method is called by the initializeSystem hook.
     *
     * @return void
     */
    public function onInitializeSystem()
    {
        // No initialization when being in install mode.
        if (TL_SCRIPT === 'contao/install.php') {
            return;
        }

        $event = new InitializeSystemEvent();
        $this->eventDispatcher->dispatch(InitializeSystemEvent::NAME, $event);
    }
}
