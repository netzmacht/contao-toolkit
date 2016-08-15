<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\DependencyInjection;

use Interop\Container\ContainerInterface;

/**
 * Provide access to the container.
 *
 * Trait should only used with care. Use dependency injection instead!
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection
 */
trait ContainerTrait
{
    /**
     * Get the container.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $GLOBALS['container'][ToolkitServices::CONTAINER];
    }
}
