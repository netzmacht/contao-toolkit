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

    /**
     * Template factory service.
     *
     * The template factory service is an instance of Netzmacht\Contao\Toolkit\View\TemplateFactory.
     *
     * @var string
     */
    const TEMPLATE_FACTORY = 'toolkit.view.template-factory';

    /**
     * Map of all template helpers.
     *
     * Is an array object of Netzmacht\Contao\Toolkit\View\ViewHelper
     *
     * @var string
     */
    const VIEW_HELPERS = 'toolkit.view.template-helpers';

    /**
     * Assets manager service.
     *
     * Is an instance of Netzmacht\Contao\Toolkit\View\Assets\
     *
     * @var string
     */
    const ASSETS_MANAGER = 'toolkit.view.assets-manager';
}
