<?php

/**
 * @package    dev-tools
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 */

namespace Netzmacht\Contao\DevTools;

/**
 * Class ServiceProviderTrait.
 *
 * Injecting the container or use the Container as a service locator is a bad thing. This is used because Contao
 * does not provide dependency injection.
 *
 * @package Netzmacht\Workflow\Contao
 */
trait ServiceContainerTrait
{
    /**
     * Get a service from the service provider.
     *
     * @param string $name Name of the service.
     *
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getService($name)
    {
        return $GLOBALS['container'][$name];
    }
}
