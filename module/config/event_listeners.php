<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Netzmacht\Contao\Toolkit\Boot\Event\InitializeSystemEvent;

global $container;

return [
    CreateFormatterEvent::NAME => [
        [$container['toolkit.dca.formatter.create-subscriber'], 'handle']
    ],
    InitializeSystemEvent::NAME => [
        [$container['toolkit.component.content-element-map-converter'], 'convert'],
        [$container['toolkit.component.module-map-converter'], 'convert'],
    ]
];
