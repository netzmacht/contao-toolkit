<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit;

use Netzmacht\Contao\Toolkit\Event\InitializeSystemEvent;

/**
 * Boot the environment.
 *
 * @package Netzmacht\Contao\Toolkit
 */
class Boot
{
    /**
     * Initialize.
     *
     * This method is called by the initializeDependencyContainer hook.
     *
     * @param \Pimple $container The dependency container.
     *
     * @return void
     */
    public function initialize(\Pimple $container)
    {
        /** @var ServiceContainer $serviceContainer */
        $serviceContainer = $container['toolkit.service-container'];

        $event = new InitializeSystemEvent($serviceContainer);
        $serviceContainer->getEventDispatcher()->dispatch($event::NAME, $event);
    }
}
