<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Options;

/**
 * Interface Options describes the options.
 *
 * @package Netzmacht\Contao\DevTools\Dca\Options
 */
interface Options extends \ArrayAccess, \Iterator
{
    /**
     * Get array copy.
     *
     * @return array
     */
    public function getArrayCopy();
}
