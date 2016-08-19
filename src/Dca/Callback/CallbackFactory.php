<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback;

use Controller;
use Netzmacht\Contao\Toolkit\Dca\Callback\Button\StateButtonCallback;
use Netzmacht\Contao\Toolkit\Dca\Callback\Wizard\ColorPicker;
use Netzmacht\Contao\Toolkit\DependencyInjection\ContainerAware;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;

/**
 * Class CallbackFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
class CallbackFactory
{
    use ContainerAware;

    /**
     * Create templates callback.
     *
     * @param string     $prefix  Template prefix to return only templates beginning with a filter.
     * @param array|null $exclude Exclude a set of template files.
     *
     * @return \Closure
     */
    public static function getTemplates($prefix = '', array $exclude = null)
    {
        return function () use ($prefix, $exclude) {
            $templates = Controller::getTemplateGroup($prefix);

            if (empty($exclude)) {
                return $templates;
            }

            return array_diff($templates, $exclude);
        };
    }

    /**
     * Create the state button toggle callback.
     *
     * @param string $dataContainerName Data Contaienr name.
     * @param string $column            State column.
     * @param null   $disabledIcon      Optional disabled icon.
     * @param bool   $inverse           If true the state value gets inversed.
     *
     * @return StateButtonCallback
     */
    public static function stateButton($dataContainerName, $column, $disabledIcon = null, $inverse = false)
    {
        $container          = static::getContainer();
        $stateToggleFactory = $container->get(Services::STATE_TOGGLE_FACTORY);
        $stateToggle        = $stateToggleFactory($dataContainerName, $column);

        return new StateButtonCallback(
            $container->get(Services::INPUT),
            $stateToggle,
            $column,
            $disabledIcon,
            $inverse
        );
    }

    /**
     * Create a callback by fetching a service.
     *
     * @param string $serviceName Name of the service.
     * @param string $methodName  Callback method name.
     *
     * @return \Closure
     */
    public static function service($serviceName, $methodName)
    {
        return function () use ($serviceName, $methodName) {
            $service = self::getContainer()->get($serviceName);

            return call_user_func_array([$service, $methodName], func_get_args());
        };
    }

    /**
     * Create the color picker callback.
     *
     * @param bool        $replaceHex Replace hex char of rgb notation.
     * @param string|null $template   Template name.
     *
     * @return ColorPicker
     */
    public static function colorPicker($replaceHex = true, $template = null)
    {
        $container = static::getContainer();

        return new ColorPicker(
            $container->get(Services::TEMPLATE_FACTORY),
            $container->get(Services::TRANSLATOR),
            $container->get(Services::INPUT),
            COLORPICKER,
            $replaceHex,
            $template
        );
    }
}
