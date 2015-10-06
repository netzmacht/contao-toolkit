<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Event;

use Netzmacht\Contao\Toolkit\ServiceContainer;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class InitializeSystemEvent.
 *
 * @package Netzmacht\Contao\Toolkit\Event
 */
class InitializeSystemEvent extends Event
{
    const NAME = 'toolkit.initialize-system';

    /**
     * Service container.
     *
     * @var ServiceContainer
     */
    private $serviceContainer;

    /**
     * InitializeSystemEvent constructor.
     *
     * @param ServiceContainer $serviceContainer Service container.
     */
    public function __construct(ServiceContainer $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Get the service container.
     *
     * @return ServiceContainer
     */
    public function getServiceContainer()
    {
        return $this->serviceContainer;
    }
}
