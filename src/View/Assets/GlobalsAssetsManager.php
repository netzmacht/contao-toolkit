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
 * Globals assets manager registers all assets as Contao globals.
 *
 * @package Netzmacht\Contao\Toolkit
 */
final class GlobalsAssetsManager implements AssetsManager
{
    /**
     * The registered stylesheets.
     *
     * @var array
     */
    private $javascripts;

    /**
     * The registered javascripts.
     *
     * @var array
     */
    private $stylesheets;

    /**
     * Debug mode of the environment.
     *
     * @var bool
     */
    private $debugMode;

    /**
     * AssetsManager constructor.
     *
     * @param array  $stylesheets The registered stylesheets.
     * @param array  $javascripts The registered javascripts.
     * @param bool   $debugMode   Debug mode of the environment.
     */
    public function __construct(array &$stylesheets, array &$javascripts, $debugMode = false)
    {
        $this->stylesheets =& $stylesheets;
        $this->javascripts =& $javascripts;
        $this->debugMode   = $debugMode;
    }

    /**
     * {@inheritDoc}
     */
    public function addJavascript(string $path, $static = self::STATIC_PRODUCTION, string $name = null): AssetsManager
    {
        if ($this->isStatic($static)) {
            $path .= '|static';
        }

        if ($name) {
            $this->javascripts[$name] = $path;
        } else {
            $this->javascripts[] = $path;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addJavascripts(array $paths, $static = self::STATIC_PRODUCTION, string $name = null): AssetsManager
    {
        foreach ($paths as $identifier => $path) {
            if ($name) {
                $name .= '_' . $identifier;
            } elseif (!is_numeric($identifier)) {
                $name = $identifier;
            }

            $this->addJavascript($path, $static, $name);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addStylesheet(
        string $path,
        string $media = '',
        $static = self::STATIC_PRODUCTION,
        string $name = null
    ): AssetsManager {
        $static = $this->isStatic($static);

        if ($media || $static) {
            $path .= '|' . $media;

            if ($static) {
                $path .= '|static';
            }
        }

        if ($name) {
            $this->stylesheets[$name] = $path;
        } else {
            $this->stylesheets[] = $path;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addStylesheets(
        array $paths,
        string $media = '',
        $static = self::STATIC_PRODUCTION,
        string $name = null
    ): AssetsManager {
        foreach ($paths as $identifier => $path) {
            if ($name) {
                $name .= '_' . $identifier;
            } elseif (!is_numeric($identifier)) {
                $name = $identifier;
            }

            $this->addStylesheet($path, $media, $static, $name);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    private function isStatic($flag): bool
    {
        if ($flag === static::STATIC_PRODUCTION) {
            return !$this->debugMode;
        }

        return (bool) $flag;
    }
}
