<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Contao\InsertTags as ContaoInsertTags;
use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Netzmacht\Contao\Toolkit\Component\FactoryToClassMapConverter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;
use Netzmacht\Contao\Toolkit\Data\State\StateToggle;
use Netzmacht\Contao\Toolkit\Dca\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\DcaLoader;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber\CreateFormatterSubscriber;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\DependencyInjection\PimpleAdapter;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;
use Netzmacht\Contao\Toolkit\InsertTag\InsertTags;
use Netzmacht\Contao\Toolkit\InsertTag\IntegratedReplacer;
use Netzmacht\Contao\Toolkit\InsertTag\Replacer;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\Contao\Toolkit\View\Assets\GlobalsAssetsManager;
use Netzmacht\Contao\Toolkit\View\Template\Subscriber\GetTemplateHelpersListener;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;

global $container;

// ---------------------------------------------------------------------------------------------------------------------
// Contao services
//
// Toolkit provides Contao singletons as service. In addition to contao-community-alliance/dependency-container missing
// classes are registered in the container.
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Contao Files as file system service.
 *
 * @return \Files
 */
$container[Services::FILE_SYSTEM] = function () {
    return \Files::getInstance();
};

/**
 * Contao Encryption class as a service.
 *
 * @return Encryption
 */
$container[Services::ENCRYPTION] = function () {
    return Encryption::getInstance();
};

/**
 * Request token as service.
 *
 * @return RequestToken
 */
$container[Services::REQUEST_TOKEN] = $container->share(
    function () {
        return RequestToken::getInstance();
    }
);


// ---------------------------------------------------------------------------------------------------------------------
// Toolkit Dependency injection
//
// Toolkit provides an container implementation which based on container-interop/container-interop ContainerInterface.
// Instead of access Pimple in user land code it's recommend to depend on ContainerInterface. Since Contao 4.x is
// supported, an adapter to Symfony container based on ContainerInterface or the updated
// contao-community-alliance/dependency-injection implementation will be provided.
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Get the container.
 *
 * @return ContainerInterface
 */
$container[Services::CONTAINER] = $container->share(
    function ($container) {
        return new PimpleAdapter($container);
    }
);


// ---------------------------------------------------------------------------------------------------------------------
// Toolkit configuration
//
// Toolkit defines it's own production mode setting. You can override it to toggle some debuge features without using
// the ugly debug toolbar of Contao.
// ---------------------------------------------------------------------------------------------------------------------

/**
 * The default production mode is based on the debug mode config.
 *
 * @param Pimple $container Pimple container.
 *
 * @return bool
 */
$container['toolkit.production-mode.default'] = function ($container) {
    return !$container['config']->get('debugMode');
};

/**
 * The production mode. If not set before the default value is used.
 * @var bool
 */
if (!isset($container[Services::PRODUCTION_MODE])) {
    $container[Services::PRODUCTION_MODE] = $container['toolkit.production-mode.default'];
}


// ---------------------------------------------------------------------------------------------------------------------
// Toolkit View system
//
// Toolkit provides several tools for the view component like a template factory and an assets manager.
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Service definition of the template factory.
 *
 * @return TemplateFactory
 */
$container[Services::TEMPLATE_FACTORY] = $container->share(
    function ($container) {
        return new TemplateFactory($container[Services::EVENT_DISPATCHER]);
    }
);

/**
 * Get the template helpers listener.
 *
 * @param \Pimple $container Pimple container.
 *
 * @return GetTemplateHelpersListener
 */
$container['toolkit.view.get-template-helpers-listener'] = function ($container) {
    return new GetTemplateHelpersListener($container[Services::CONTAINER]);
};

/**
 * Service definition of the assets manager.
 *
 * @return AssetsManager
 */
$container[Services::ASSETS_MANAGER] = $container->share(
    function ($container) {
        return new GlobalsAssetsManager(
            $GLOBALS['TL_CSS'],
            $GLOBALS['TL_JAVASCRIPT'],
            $container['toolkit.production-mode']
        );
    }
);


// ---------------------------------------------------------------------------------------------------------------------
// Toolkit components
//
// Toolkit provides an handy way to create decoupled components. Therefor required services are defined. Typically you
// don't have to use them. The toolkit system works out of the box. Please consider the documentation for more details.
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Module factory.
 *
 * @return ComponentFactory
 */
$container[Services::MODULE_FACTORY] = $container->share(
    function ($container) {
        return new ComponentFactory(
            $container[Services::MODULES_MAP],
            $container[Services::CONTAINER]
        );
    }
);

/**
 * Modules map.
 *
 * @return ArrayObject
 */
$container[Services::MODULES_MAP] = $container->share(
    function () {
        return new ArrayObject();
    }
);

/**
 * Content element config converter.
 *
 * @return FactoryToClassMapConverter
 */
$container['toolkit.component.module-map-converter'] = $container->share(
    function ($container) {
        return new FactoryToClassMapConverter(
            $GLOBALS['FE_MOD'],
            $container[Services::MODULES_MAP],
            'Netzmacht\Contao\Toolkit\Component\Module\ModuleDecorator'
        );
    }
);

/**
 * Content element factory.
 *
 * @param $container
 *
 * @return bool
 */
$container[Services::CONTENT_ELEMENT_FACTORY] = $container->share(
    function ($container) {
        return new ComponentFactory(
            $container[Services::CONTENT_ELEMENTS_MAP],
            $container[Services::CONTAINER]
        );
    }
);

/**
 * Content elements map.
 *
 * @return ArrayObject
 */
$container[Services::CONTENT_ELEMENTS_MAP] = $container->share(
    function () {
        return new ArrayObject();
    }
);

/**
 * Content element config converter
 *
 * @return FactoryToClassMapConverter
 */
$container['toolkit.component.content-element-map-converter'] = $container->share(
    function ($container) {
        return new FactoryToClassMapConverter(
            $GLOBALS['TL_CTE'],
            $container[Services::CONTENT_ELEMENTS_MAP],
            'Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElementDecorator'
        );
    }
);


// ---------------------------------------------------------------------------------------------------------------------
// Toolkit dca system
//
// Toolkit provides useful tools to ease working with dca files. There's an dca manager which provides access to dca
// definitions and dca based formatter.
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Dca manager.
 *
 * @return Manager
 */
$container[Services::DCA_MANAGER] = $container->share(
    function ($container) {
        return new Manager(
            $container['toolkit.dca-loader'],
            $container['toolkit.dca.formatter.factory']
        );
    }
);

/**
 * Callback invoker.
 *
 * @return Invoker
 */
$container[Services::CALLBACK_INVOKER] = $container->share(
    function () {
        return new Invoker();
    }
);

/**
 * Dca loader for loading dca files.
 *
 * @return DcaLoader
 */
$container['toolkit.dca-loader'] = function () {
    return new DcaLoader();
};

/**
 * State toggle factory.
 *
 * @return StateToggle
 */
$container[Services::STATE_TOGGLE] = $container->share(
    function ($container) {
        return new StateToggle(
            $container[Services::USER],
            $container[Services::DATABASE_CONNECTION],
            $container[Services::DCA_MANAGER],
            $container[Services::CALLBACK_INVOKER]
        );
    }
);

/**
 * Event listener to create all default formatter.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return CreateFormatterSubscriber
 */
$container['toolkit.dca.formatter.create-subscriber'] = $container->share(
    function ($container) {
        return new CreateFormatterSubscriber(
            $container[Services::CONTAINER],
            $container['toolkit.dca-formatter.service-names']
        );
    }
);

/**
 * Formatter factory factory
 *
 * @param \Pimple $container Dependency container.
 *
 * @return FormatterFactory
 */
$container['toolkit.dca.formatter.factory'] = $container->share(
    function ($container) {
        return new FormatterFactory($container[Services::CONTAINER], $container[Services::EVENT_DISPATCHER]);
    }
);

/**
 * Get the default alias generator factory.
 *
 * @return callable
 */
$container[Services::DEFAULT_ALIAS_GENERATOR_FACTORY] = $container->share(
    function ($container) {
        return function ($dataContainerName, $aliasField, $fields) use ($container) {
            $filters = [
                new ExistingAliasFilter(),
                new SlugifyFilter($fields),
                new SuffixFilter()
            ];

            $validator = new UniqueDatabaseValueValidator(
                $container[Services::DATABASE_CONNECTION],
                $dataContainerName,
                $aliasField
            );

            return new Generator($filters, $validator, $dataContainerName, $aliasField);
        };
    }
);


// ---------------------------------------------------------------------------------------------------------------------
// Toolkit insert tag system
//
// Toolkit provides an interface for insert tag parsers and an insert tag replacer service.
// ---------------------------------------------------------------------------------------------------------------------

/**
 * Service definition of the insert tag replacer.
 *
 * @return Replacer
 */
$container[Services::INSERT_TAG_REPLACER] = $container->share(
    function ($container) {
        $insertTags = $container['toolkit.insert-tag.insert-tags'];
        $replacer   = new IntegratedReplacer($insertTags);

        $GLOBALS['TL_HOOKS']['replaceInsertTags'][] = [get_class($replacer), 'replaceTag'];

        return $replacer;
    }
);

/**
 * Get insert tag replace of Contao.
 *
 * @return InsertTags|ContaoInsertTags
 */
$container['toolkit.insert-tag.insert-tags'] = $container->share(
    function () {
        if (version_compare(VERSION . '.' . BUILD, '3.5.3', '>=')) {
            return new ContaoInsertTags();
        }

        return new InsertTags();
    }
);
