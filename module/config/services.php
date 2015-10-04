<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

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
use Netzmacht\Contao\Toolkit\ServiceContainer;
use Netzmacht\Contao\Toolkit\View\AssetsManager;

global $container;

$container['toolkit.production-mode.default'] = function ($container) {
    return !$container['config']->get('debugMode');
};

if (!isset($container['toolkit.production-mode'])) {
    $container['toolkit.production-mode'] = $container['toolkit.production-mode.default'];
}

$container['toolkit.dca-loader'] = function () {
    return new DcaLoader();
};

$container['toolkit.dca-manager'] = $container->share(
    function ($container) {
        return new Manager($container['toolkit.dca-loader'], $container['toolkit.dca-formatter.factory']);
    }
);

$container['toolkit.assets-manager'] = $container->share(
    function ($container) {
        return new AssetsManager(
            $GLOBALS['TL_CSS'],
            $GLOBALS['TL_JAVASCRIPT'],
            $container['toolkit.production-mode']
        );
    }
);

$container['toolkit.filesystem'] = $container->share(
    function () {
        return \Files::getInstance();
    }
);

$container['toolkit.service-container'] = $container->share(
    function ($container) {
        return new ServiceContainer($container);
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
        return new FormatterFactory($container['toolkit.service-container'], $container['event-dispatcher']);
    }
);
