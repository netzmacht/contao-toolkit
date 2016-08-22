<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElementDecorator;
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
use Netzmacht\Contao\Toolkit\InsertTag\IntegratedReplacer;
use Netzmacht\Contao\Toolkit\InsertTag\Replacer;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;

global $container;

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

/**
 * Service definition of the template factory.
 *
 * @return TemplateFactory
 */
$container[Services::TEMPLATE_FACTORY] = $container->share(
    function ($container) {
        return new TemplateFactory($container[Services::VIEW_HELPERS]);
    }
);

/**
 * Service definition of the view helpers factory map.
 *
 * @return ArrayObject
 */
$container[Services::VIEW_HELPERS] = $container->share(
    function ($container) {
        return new ArrayObject(
            [
                'translator' => $container[Services::TRANSLATOR],
                'assets'     => $container[Services::ASSETS_MANAGER],
            ]
        );
    }
);

/**
 * Service definition of the assets manager.
 *
 * @return AssetsManager
 */
$container[Services::ASSETS_MANAGER] = $container->share(
    function ($container) {
        return new AssetsManager(
            $GLOBALS['TL_CSS'],
            $GLOBALS['TL_JAVASCRIPT'],
            $container['toolkit.production-mode']
        );
    }
);

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
 * Content element config converter
 *
 * @return ContentElementDecorator
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
 * @return ContentElementDecorator
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

$container['toolkit.production-mode.default'] = function ($container) {
    return !$container['config']->get('debugMode');
};

if (!isset($container['toolkit.production-mode'])) {
    $container['toolkit.production-mode'] = $container['toolkit.production-mode.default'];
}

$container['toolkit.dca-loader'] = function () {
    return new DcaLoader();
};

$container[Services::DCA_MANAGER] = $container->share(
    function ($container) {
        return new Manager(
            $container['toolkit.dca-loader'],
            $container['toolkit.dca.formatter.factory']
        );
    }
);

$container[Services::CALLBACK_INVOKER] = $container->share(
    function () {
        return new Invoker();
    }
);

$container[Services::FILE_SYSTEM] = $container->share(
    function () {
        return \Files::getInstance();
    }
);

$container[Services::ENCRYPTION] = function () {
    return Encryption::getInstance();
};

/**
 * State toggle factory.
 *
 * @return callable
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
 * Service definition of the insert tag replacer.
 *
 * @return Replacer
 */
$container[Services::INSERT_TAG_REPLACER] = $container->share(
    function () {
        if (version_compare(VERSION . '.' . BUILD, '3.5.3', '>=')) {
            $insertTags = new \Contao\InsertTags();
        } else {
            $insertTags = new \Netzmacht\Contao\Toolkit\InsertTag\InsertTags();
        }

        $replacer                                   = new IntegratedReplacer($insertTags);
        $GLOBALS['TL_HOOKS']['replaceInsertTags'][] = [get_class($replacer), 'replaceTag'];

        return $replacer;
    }
);

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
