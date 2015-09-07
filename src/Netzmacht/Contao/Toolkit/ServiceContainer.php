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

use Netzmacht\Contao\Toolkit\Dca\Manager;

/**
 * The toolkit service container.
 *
 * @package Netzmacht\Contao\Toolkit
 */
class ServiceContainer
{
    /**
     * The service container.
     *
     * @var \Pimple
     */
    private $container;

    /**
     * ServiceContainer constructor.
     *
     * @param \Pimple $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Get a service from the container.
     *
     * @param string $service The service name.
     *
     * @return mixed
     */
    public function getService($service)
    {
        return $this->container[$service];
    }

    /**
     * Get the dca manager.
     *
     * @return Manager
     */
    public function getDcaManager()
    {
        return $this->getService('toolkit.dca-manager');
    }
}
