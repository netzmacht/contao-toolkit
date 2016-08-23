<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
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
trait ContainerAware
{
    /**
     * Get the container.
     *
     * @return ContainerInterface
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected static function getContainer()
    {
        return $GLOBALS['container'][Services::CONTAINER];
    }
}
