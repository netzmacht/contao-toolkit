<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit;

/**
 * Simple interface to handle assets registration.
 *
 * @package Netzmacht\Contao\Toolkit
 *
 * @deprecated Use Netzmacht\Contao\Toolkit\View\AssetsManager
 */
class Assets
{
    use ServiceContainerTrait;

    const STATIC_PRODUCTION = 'prod';

    /**
     * Add a javascript file to Contao assets.
     *
     * @param string $path   The assets path.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return void
     */
    public static function addJavascript($path, $static = self::STATIC_PRODUCTION, $name = null)
    {
        static::getServiceContainer()->getAssetsManager()->addJavascript($path, $static, $name);
    }

    /**
     * Add javascript files to Contao assets.
     *
     * @param array  $paths  The assets paths.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return void
     */
    public static function addJavascripts(array $paths, $static = self::STATIC_PRODUCTION, $name = null)
    {
        static::getServiceContainer()->getAssetsManager()->addJavascripts($paths, $static, $name);
    }

    /**
     * Add a javascript file to Contao assets.
     *
     * @param string $path   The assets path.
     * @param string $media  The media query.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return void
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function addStylesheet($path, $media = '', $static = self::STATIC_PRODUCTION, $name = null)
    {
        static::getServiceContainer()->getAssetsManager()->addStylesheet($path, $media, $static, $name);
    }

    /**
     * Add stylesheet files to Contao assets.
     *
     * @param array  $paths  The assets paths.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return void
     */
    public static function addStylesheets(array $paths, $static = self::STATIC_PRODUCTION, $name = null)
    {
        static::getServiceContainer()->getAssetsManager()->addStylesheets($paths, '', $static, $name);
    }
}
