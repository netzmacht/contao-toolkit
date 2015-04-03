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
 */
class Assets
{
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
        if (static::isStatic($static)) {
            $path .= '|static';
        }

        if ($name) {
            $GLOBALS['TL_JAVASCRIPT'][$name] = $path;
        } else {
            $GLOBALS['TL_JAVASCRIPT'][] = $path;
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
    public static function addStylesheet($path, $media = '', $static = self::STATIC_PRODUCTION, $name = null)
    {
        $static = static::isStatic($static);

        if ($media || $static) {
            $path .= '|' . $media;

            if ($static) {
                $path .= '|static';
            }
        }

        if ($name) {
            $GLOBALS['TL_CSS'][$name] = $path;
        } else {
            $GLOBALS['TL_CSS'][] = $path;
        }
    }

    /**
     * Evaluate the static flag by recognizing the debug mode setting.
     *
     * @param mixed $flag The static flag.
     *
     * @return bool
     */
    private static function isStatic($flag)
    {
        if ($flag === static::STATIC_PRODUCTION) {
            return !\Config::get('debugMode');
        }

        return $flag;
    }
}
