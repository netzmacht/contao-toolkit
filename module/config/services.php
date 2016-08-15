<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Interop\Container\ContainerInterface;
use Interop\Container\Pimple\PimpleInterop;
use Netzmacht\Contao\Toolkit\Dca\DcaLoader;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DateFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DeserializeFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\EncryptedFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FileUuidFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FilterFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FlattenFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ForeignKeyFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FormatterChain;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HiddenValueFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HtmlFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\OptionsFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ReferenceFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\YesNoFormatter;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;
use Netzmacht\Contao\Toolkit\InsertTag\IntegratedReplacer;
use Netzmacht\Contao\Toolkit\ServiceContainer;
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
        return new PimpleInterop($container);
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
    function () {
        return new ArrayObject();
    }
);

/**
 * Factory definition of the translator view helper.
 *
 * @return TranslatorInterface
 */
$container[Services::VIEW_HELPERS]['translator'] = function () use ($container) {
    return $container[Services::TRANSLATOR];
};

/**
 * Factory definition of the assets manager view helper.
 *
 * @return AssetsManager
 */
$container[Services::VIEW_HELPERS]['assets'] = function () use ($container) {
    return $container[Services::ASSETS_MANAGER];
};

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
        return new Manager($container['toolkit.dca-loader'], $container['toolkit.dca-formatter.factory']);
    }
);

$container[Services::FILE_SYSTEM] = $container->share(
    function () {
        return \Files::getInstance();
    }
);

/**
 * Date formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.date'] = function ($container) {
    return new DateFormatter($container['config']);
};

/**
 * Encyrpted formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.encrypted'] = function () {
    return new EncryptedFormatter();
};

/**
 * FileUuid formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.file-uuid'] = function () {
    return new FileUuidFormatter();
};

/**
 * ForeignKey formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.foreign-key'] = function ($container) {
    return new ForeignKeyFormatter($container['database.connection']);
};

/**
 * Hidden formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.hidden'] = function () {
    return new HiddenValueFormatter();
};

/**
 * Options formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.options'] = function () {
    return new OptionsFormatter();
};

/**
 * Reference formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.reference'] = function () {
    return new ReferenceFormatter();
};

/**
 * YesNo formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.yes-no'] = function ($container) {
    return new YesNoFormatter($container['translator']);
};

/**
 * HTML formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.html'] = function () {
    return new HtmlFormatter();
};

/**
 * Deserialize formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.deserialize'] = function () {
    return new DeserializeFormatter();
};

/**
 * Flatter formatter factory.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.flatten'] = function () {
    return new FlattenFormatter();
};

/**
 * Pre filter formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.pre-filter'] = function ($container) {
    return new FilterFormatter(
        [
            $container['toolkit.dca-formatter.hidden'],
            $container['toolkit.dca-formatter.deserialize'],
            $container['toolkit.dca-formatter.encrypted'],
        ]
    );
};

/**
 * Post filter formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.post-filter'] = function ($container) {
    return new FilterFormatter([$container['toolkit.dca-formatter.flatten']]);
};

/**
 * Default formatter factory.
 *
 * @param \Pimple $container Dependency container.
 *
 * @return ValueFormatter
 */
$container['toolkit.dca-formatter.default'] = $container->share(
    function ($container) {
        return new FormatterChain(
            [
                $container['toolkit.dca-formatter.foreign-key'],
                $container['toolkit.dca-formatter.file-uuid'],
                $container['toolkit.dca-formatter.date'],
                $container['toolkit.dca-formatter.yes-no'],
                $container['toolkit.dca-formatter.html'],
                $container['toolkit.dca-formatter.reference'],
                $container['toolkit.dca-formatter.options'],
            ]
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
$container['toolkit.dca-formatter.factory'] = $container->share(
    function ($container) {
        return new FormatterFactory($container[Services::CONTAINER], $container[Services::EVENT_DISPATCHER]);
    }
);

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
