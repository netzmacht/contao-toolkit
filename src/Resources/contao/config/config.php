<?php

/**
 * contao-toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = [
    'netzmacht.contao_toolkit.listeners.set_operation_data_attribute',
    'onLoadDataContainer'
];
