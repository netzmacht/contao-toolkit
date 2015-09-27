<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca;

use Netzmacht\Contao\Toolkit\TranslatorTrait;

/**
 * Base class for data container callback classes.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
abstract class Callbacks
{
    use TranslatorTrait;

    /**
     * Generate the callback definition.
     *
     * @param string $name Callback method.
     *
     * @return array
     */
    public static function callback($name)
    {
        return [get_called_class(), $name];
    }
}
