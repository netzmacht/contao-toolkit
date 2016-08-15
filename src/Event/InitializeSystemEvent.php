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

use Interop\Container\ContainerInterface as Container;
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
     * @var Container
     */
    private $container;

    /**
     * InitializeSystemEvent constructor.
     *
     * @param Container $container Service container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get the service container.
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
}
