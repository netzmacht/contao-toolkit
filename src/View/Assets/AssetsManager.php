<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\View\Assets;

/**
 * Assets manager describes an asset manager which handles assets being added by components.
 *
 * @package Netzmacht\Contao\Toolkit
 */
interface AssetsManager
{
    const STATIC_PRODUCTION = 'prod';

    /**
     * Add a javascript file to Contao assets.
     *
     * @param string $path   The assets path.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return $this
     */
    public function addJavascript($path, $static = self::STATIC_PRODUCTION, $name = null);

    /**
     * Add javascript files to Contao assets.
     *
     * @param array  $paths  The assets paths.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return $this
     */
    public function addJavascripts(array $paths, $static = self::STATIC_PRODUCTION, $name = null);

    /**
     * Add a javascript file to Contao assets.
     *
     * @param string $path   The assets path.
     * @param string $media  The media query.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return $this
     */
    public function addStylesheet($path, $media = '', $static = self::STATIC_PRODUCTION, $name = null);

    /**
     * Add stylesheet files to Contao assets.
     *
     * @param array  $paths  The assets paths.
     * @param string $media  The media type.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return $this
     */
    public function addStylesheets(array $paths, $media = '', $static = self::STATIC_PRODUCTION, $name = null);
}
