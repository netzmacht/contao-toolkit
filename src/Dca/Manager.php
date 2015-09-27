<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca;

/**
 * Data container definition manager.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
class Manager
{
    /**
     * Data definition cache.
     *
     * @var Definition[]
     */
    private $definitions = array();

    /**
     * The data definition array loader.
     *
     * @var DcaLoader
     */
    private $loader;

    /**
     * Manager constructor.
     *
     * @param DcaLoader $loader The data definition array loader.
     */
    public function __construct(DcaLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get a data container definition.
     *
     * @param string $name    The data definition name.
     * @param bool   $noCache If true not the cached version is loaded.
     *
     * @return Definition
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function get($name, $noCache = false)
    {
        if (!$noCache) {
            $this->loader->loadDataContainer($name, $noCache);

            return new Definition($GLOBALS['TL_DCA'][$name]);
        }

        if (!isset($this->definitions[$name])) {
            $this->loader->loadDataContainer($name);

            $this->definitions[$name] = new Definition($name);
        }

        return $this->definitions[$name];
    }
}
