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

use Netzmacht\Contao\Toolkit\DependencyInjection\ContainerAware;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;
use Netzmacht\Contao\Toolkit\Boot\Event\InitializeSystemEvent;

/**
 * Boot the environment.
 *
 * @package Netzmacht\Contao\Toolkit
 */
final class Boot
{
    use ContainerAware;

    /**
     * Initialize.
     *
     * This method is called by the initializeDependencyContainer hook.
     *
     * @return void
     */
    public function initialize()
    {
        // No initialization when being in install mode.
        if (TL_SCRIPT === 'contao/install.php') {
            return;
        }

        $container  = static::getContainer();
        $event      = new InitializeSystemEvent($container);
        $dispatcher = $container->get(Services::EVENT_DISPATCHER);

        $dispatcher->dispatch($event::NAME, $event);
    }
}
