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
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\ServiceContainer;

global $container;

$container['toolkit.dca-loader'] = function () {
    return new DcaLoader();
};

$container['toolkit.dca-manager'] = $container->share(
    function ($container) {
        return new Manager($container['toolkit.dca-loader']);
    }
);

$container['toolkit.service-container'] = $container->share(
    function ($container) {
        return new ServiceContainer($container);
    }
);
