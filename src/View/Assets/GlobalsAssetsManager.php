<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

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
     * Production mode of the environment.
     *
     * @var bool
     */
    private $productionMode;

    /**
     * AssetsManager constructor.
     *
     * @param array $stylesheets    The registered stylesheets.
     * @param array $javascripts    The registered javascripts.
     * @param bool  $productionMode Production mode of the environment.
     */
    public function __construct(&$stylesheets, &$javascripts, $productionMode = false)
    {
        $this->stylesheets    =& $stylesheets;
        $this->javascripts    =& $javascripts;
        $this->productionMode = $productionMode;
    }

    /**
     * {@inheritDoc}
     */
    public function addJavascript($path, $static = self::STATIC_PRODUCTION, $name = null)
    {
        if (static::isStatic($static)) {
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
    public function addJavascripts(array $paths, $static = self::STATIC_PRODUCTION, $name = null)
    {
        foreach ($paths as $identifier => $path) {
            if ($name) {
                $name .= '_' . $identifier;
            } elseif (!is_numeric($identifier)) {
                $name = $identifier;
            }

            static::addJavascript($path, $static, $name);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addStylesheet($path, $media = '', $static = self::STATIC_PRODUCTION, $name = null)
    {
        $static = static::isStatic($static);

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
    public function addStylesheets(array $paths, $media = '', $static = self::STATIC_PRODUCTION, $name = null)
    {
        foreach ($paths as $identifier => $path) {
            if ($name) {
                $name .= '_' . $identifier;
            } elseif (!is_numeric($identifier)) {
                $name = $identifier;
            }

            static::addStylesheet($path, $media, $static, $name);
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    private function isStatic($flag)
    {
        if ($flag === static::STATIC_PRODUCTION) {
            return $this->productionMode;
        }

        return $flag;
    }
}
