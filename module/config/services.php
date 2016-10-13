<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Contao\InsertTags as ContaoInsertTags;
use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Netzmacht\Contao\Toolkit\Component\FactoryToClassMapConverter;
use Netzmacht\Contao\Toolkit\Component\ToolkitComponentFactory;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\FilterBasedAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;
use Netzmacht\Contao\Toolkit\Data\Updater\DatabaseRowUpdater;
use Netzmacht\Contao\Toolkit\Data\Updater\Updater;
use Netzmacht\Contao\Toolkit\Dca\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\DcaLoader;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber\CreateFormatterSubscriber;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DateFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DeserializeFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\EncryptedFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FileUuidFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FlattenFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ForeignKeyFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HiddenValueFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HtmlFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\OptionsFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ReferenceFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\YesNoFormatter;
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
use Netzmacht\Contao\Toolkit\View\Template\ToolkitTemplateFactory;

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
        return new ToolkitTemplateFactory($container[Services::EVENT_DISPATCHER]);
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
        if (!isset($GLOBALS['TL_CSS']) || !is_array($GLOBALS['TL_CSS'])) {
            $GLOBALS['TL_CSS'] = [];
        }

        if (!isset($GLOBALS['TL_JAVASCRIPT']) || !is_array($GLOBALS['TL_JAVASCRIPT'])) {
            $GLOBALS['TL_JAVASCRIPT'] = [];
        }

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
        return new ToolkitComponentFactory(
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
 * @return ComponentFactory
 */
$container[Services::CONTENT_ELEMENT_FACTORY] = $container->share(
    function ($container) {
        return new ToolkitComponentFactory(
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
 * Database row updater.
 *
 * @return Updater
 */
$container[Services::DATABASE_ROW_UPDATER] = $container->share(
    function ($container) {
        return new DatabaseRowUpdater(
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
            $container[Services::CONTAINER]
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
 * Date formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.date'] = $container->share(
    function ($container) {
        return new DateFormatter($container['config']);
    }
);

/**
 * Encyrpted formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.encrypted'] = $container->share(
    function ($container) {
        return new EncryptedFormatter($container[Services::ENCRYPTION]);
    }
);

/**
 * FileUuid formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.file-uuid'] = $container->share(
    function () {
        return new FileUuidFormatter();
    }
);

/**
 * ForeignKey formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.foreign-key'] = $container->share(
    function ($container) {
        return new ForeignKeyFormatter($container['database.connection']);
    }
);

/**
 * Hidden formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.hidden'] = $container->share(
    function () {
        return new HiddenValueFormatter();
    }
);

/**
 * Options formatter factory.
 *
 * @param \Pimple $container Container invoker.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.options'] = $container->share(
    function ($container) {
        return new OptionsFormatter($container[Services::CALLBACK_INVOKER]);
    }
);

/**
 * Reference formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.reference'] = $container->share(
    function () {
        return new ReferenceFormatter();
    }
);

/**
 * YesNo formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.yes-no'] = $container->share(
    function ($container) {
        return new YesNoFormatter($container['translator']);
    }
);

/**
 * HTML formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.html'] = $container->share(
    function () {
        return new HtmlFormatter();
    }
);

/**
 * Deserialize formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.deserialize'] = $container->share(
    function () {
        return new DeserializeFormatter();
    }
);

/**
 * Flatter formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca.formatter.flatten'] = $container->share(
    function () {
        return new FlattenFormatter();
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

            return new FilterBasedAliasGenerator($filters, $validator, $dataContainerName, $aliasField);
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
        // DON'T USE Contao insert tags as it breaks object stack order (session error!).
//        if (version_compare(VERSION . '.' . BUILD, '3.5.3', '>=')) {
//            return new ContaoInsertTags();
//        }

        return new InsertTags();
    }
);
