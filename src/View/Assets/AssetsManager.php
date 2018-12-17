<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

/**
 * Assets manager describes an asset manager which handles assets being added by components.
 *
 * @package Netzmacht\Contao\Toolkit
 */
interface AssetsManager
{
    public const STATIC_PRODUCTION = 'prod';

    /**
     * Add a javascript file to Contao assets.
     *
     * @param string      $path   The assets path.
     * @param string|bool $static Register it as static entry.
     * @param string|null $name   Optional assets name.
     *
     * @return $this
     */
    public function addJavascript(string $path, $static = self::STATIC_PRODUCTION, string $name = null): self;

    /**
     * Add javascript files to Contao assets.
     *
     * @param array       $paths  The assets paths.
     * @param string|bool $static Register it as static entry.
     * @param string|null $name   Optional assets name.
     *
     * @return $this
     */
    public function addJavascripts(array $paths, $static = self::STATIC_PRODUCTION, string $name = null): self;

    /**
     * Add a javascript file to Contao assets.
     *
     * @param string      $path   The assets path.
     * @param string      $media  The media query.
     * @param string|bool $static Register it as static entry.
     * @param string|null $name   Optional assets name.
     *
     * @return $this
     */
    public function addStylesheet(
        string $path,
        string $media = '',
        $static = self::STATIC_PRODUCTION,
        string $name = null
    ): self;

    /**
     * Add stylesheet files to Contao assets.
     *
     * @param array       $paths  The assets paths.
     * @param string      $media  The media type.
     * @param string|bool $static Register it as static entry.
     * @param string|null $name   Optional assets name.
     *
     * @return $this
     */
    public function addStylesheets(
        array $paths,
        string $media = '',
        $static = self::STATIC_PRODUCTION,
        string $name = null
    ): self;
}
