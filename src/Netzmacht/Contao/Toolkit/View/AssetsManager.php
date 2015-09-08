<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View;

/**
 * Simple manager to handle assets registration.
 *
 * @package Netzmacht\Contao\Toolkit
 */
class AssetsManager
{
    const STATIC_PRODUCTION = 'prod';

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
     * Add a javascript file to Contao assets.
     *
     * @param string $path   The assets path.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return void
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
    }

    /**
     * Add stylesheet files to Contao assets.
     *
     * @param array  $paths  The assets paths.
     * @param string $media  The media type.
     * @param string $static Register it as static entry.
     * @param null   $name   Optional assets name.
     *
     * @return void
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
    }

    /**
     * Evaluate the static flag by recognizing the debug mode setting.
     *
     * @param mixed $flag The static flag.
     *
     * @return bool
     */
    private function isStatic($flag)
    {
        if ($flag === static::STATIC_PRODUCTION) {
            return $this->productionMode;
        }

        return $flag;
    }
}
