<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;

global $container;

return [
    CreateFormatterEvent::NAME => [
        $container['toolkit.dca.formatter.create-subscriber']
    ]
];
