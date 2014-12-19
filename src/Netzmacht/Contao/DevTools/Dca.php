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


/**
 * Class Loader loads the data container.
 *
 * @package Netzmacht\Contao\DevTools\Dca
 */
class DcaLoader extends \Controller
{
    /**
     * Construct.
     */
    public function __construct()
    {
        // Override it to make it public.

        parent::__construct();
    }
}
