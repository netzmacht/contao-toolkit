<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\DevTools;

use Netzmacht\Contao\DevTools\Dca\DcaLoader;

/**
 * Class Dca simplifies DCA access.
 *
 * @package Netzmacht\Contao\DevTools
 */
class Dca
{
    /**
     * Load the data container.
     *
     * @param string $name        The data container name.
     * @param bool   $ignoreCache Ignore the Contao cache.
     *
     * @return void
     */
    public static function load($name, $ignoreCache = false)
    {
        $loader = new DcaLoader();
        $loader->loadDataContainer($name, $ignoreCache);
    }
}
