<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
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
final class GlobalsAssetsManager implements HtmlPageAssetsManager
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
     * The registered body content.
     *
     * @var array|string[]
     */
    private $body;

    /**
     * The registered head content.
     *
     * @var array|string[]
     */
    private $head;

    /**
     * Debug mode of the environment.
     *
     * @var bool
     */
    private $debugMode;

    /**
     * AssetsManager constructor.
     *
     * @param array $stylesheets The registered stylesheets.
     * @param array $javascripts The registered javascripts.
     * @param array $head        The registered body content.
     * @param array $body        The registered head content.
     * @param bool  $debugMode   Debug mode of the environment.
     */
    public function __construct(
        array &$stylesheets,
        array &$javascripts,
        array &$head,
        array &$body,
        $debugMode = false
    ) {
        $this->stylesheets =& $stylesheets;
        $this->javascripts =& $javascripts;
        $this->debugMode   = $debugMode;
        $this->head        = $head;
        $this->body        = $body;
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
    public function addToBody(string $name, string $html): HtmlPageAssetsManager
    {
        $this->body[$name] = $html;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function appendToBody(string $html): HtmlPageAssetsManager
    {
        $this->body[] = $html;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addToHead(string $name, string $html): HtmlPageAssetsManager
    {
        $this->head[$name] = $html;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function appendToHead(string $html): HtmlPageAssetsManager
    {
        $this->head[] = $html;

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
