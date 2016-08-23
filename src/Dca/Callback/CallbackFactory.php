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
use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;
use Netzmacht\Contao\Toolkit\Dca\Callback\Button\StateButtonCallback;
use Netzmacht\Contao\Toolkit\Dca\Callback\Save\GenerateAliasCallback;
use Netzmacht\Contao\Toolkit\Dca\Callback\Wizard\ColorPicker;
use Netzmacht\Contao\Toolkit\Dca\Callback\Wizard\FilePicker;
use Netzmacht\Contao\Toolkit\Dca\Callback\Wizard\PagePicker;
use Netzmacht\Contao\Toolkit\Dca\Callback\Wizard\PopupWizard;
use Netzmacht\Contao\Toolkit\DependencyInjection\ContainerAware;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;

/**
 * Class CallbackFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
final class CallbackFactory
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
     * @return callable
     */
    public static function stateButton($dataContainerName, $column, $disabledIcon = null, $inverse = false)
    {
        $container = static::getContainer();

        return new StateButtonCallback(
            $container->get(Services::INPUT),
            $container->get(Services::DATABASE_ROW_UPDATER),
            $dataContainerName,
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
    public static function colorPicker($replaceHex = false, $template = null)
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

    /**
     * Create the file picker callback.
     *
     * @param string|null $template Template name.
     *
     * @return FilePicker
     */
    public static function filePicker($template = null)
    {
        $container = static::getContainer();

        return new FilePicker(
            $container->get(Services::TEMPLATE_FACTORY),
            $container->get(Services::TRANSLATOR),
            $container->get(Services::INPUT),
            $template
        );
    }

    /**
     * Create the page picker callback.
     *
     * @param string|null $template Template name.
     *
     * @return PagePicker
     */
    public static function pagePicker($template = null)
    {
        $container = static::getContainer();

        return new PagePicker(
            $container->get(Services::TEMPLATE_FACTORY),
            $container->get(Services::TRANSLATOR),
            $container->get(Services::INPUT),
            $template
        );
    }

    /**
     * Create the popup wizard.
     *
     * @param string $href        Link href snippet.
     * @param string $label       Button label.
     * @param string $title       Button title.
     * @param string $icon        Button icon.
     * @param bool   $always      If true the button is generated always no matter if an value is given.
     * @param string $linkPattern Link pattern.
     * @param string $template    Template name.
     *
     * @return PopupWizard
     */
    public static function popupWizard(
        $href,
        $label,
        $title,
        $icon,
        $always = false,
        $linkPattern = null,
        $template = null
    ) {
        $container = static::getContainer();

        return new PopupWizard(
            $container->get(Services::TEMPLATE_FACTORY),
            $container->get(Services::TRANSLATOR),
            $container->get(Services::REQUEST_TOKEN),
            $href,
            $label,
            $title,
            $icon,
            $always,
            $linkPattern,
            $template
        );
    }

    /**
     * Generate the alias generator callback.
     *
     * @param string $dataContainerName Data Container name.
     * @param string $aliasField        Alias field.
     * @param array  $fields            List of fields being combined as alias. If empty ['id'] is used.
     * @param string $factoryService    Custom alias generator factory service.
     *
     * @return callable
     */
    public static function aliasGenerator($dataContainerName, $aliasField, array $fields = null, $factoryService = null)
    {
        $container      = static::getContainer();
        $factoryService = $factoryService ?: Services::DEFAULT_ALIAS_GENERATOR_FACTORY;
        $factory        = $container->get($factoryService);
        $fields         = $fields ?: ['id'];

        /** @var AliasGenerator $aliasGenerator */
        $aliasGenerator = $factory($dataContainerName, $aliasField, $fields);

        return new GenerateAliasCallback($aliasGenerator);
    }
}
