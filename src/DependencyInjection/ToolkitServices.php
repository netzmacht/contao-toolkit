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
     * Template helper service.
     *
     * The template helper service is an instance of Netzmacht\Contao\Toolkit\View\TemplateHelper.
     *
     * @var string
     */
    const VIEW_HELPER = 'toolkit.view.template-helper';

    /**
     * Map of all template helpers.
     *
     * Is an instance of
     *
     * @var string
     */
    const VIEW_HELPERS = 'toolkit.view.template-helpers';
}
