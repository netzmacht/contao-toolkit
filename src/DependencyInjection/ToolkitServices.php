<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\DependencyInjection;

/**
 * Class ToolkitServices.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection
 */
class ToolkitServices
{
    /**
     * The container provides access to the toolkit container.
     *
     * The container is an instance of Interop\Container\ContainerInterface.
     *
     * @var string
     */
    const CONTAINER = 'toolkit.container';
}
