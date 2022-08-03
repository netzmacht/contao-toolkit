<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

use function is_numeric;

/**
 * Globals assets manager registers all assets as Contao globals.
 */
final class GlobalsAssetsManager implements HtmlPageAssetsManager
{
    /**
     * The registered stylesheets.
     *
     * @var array<int|string,string>
     */
    private array $javascripts;

    /**
     * The registered javascripts.
     *
     * @var array<int|string,string>
     */
    private array $stylesheets;

    /**
     * The registered body content.
     *
     * @var array|string[]
     */
    private array $body;

    /**
     * The registered head content.
     *
     * @var array|string[]
     */
    private array $head;

    /**
     * Debug mode of the environment.
     */
    private bool $debugMode;

    /**
     * @param array<int|string,string> $stylesheets The registered stylesheets.
     * @param array<int|string,string> $javascripts The registered javascripts.
     * @param array<int|string,string> $head        The registered body content.
     * @param array<int|string,string> $body        The registered head content.
     * @param bool                     $debugMode   Debug mode of the environment.
     */
    public function __construct(
        array &$stylesheets,
        array &$javascripts,
        array &$head,
        array &$body,
        $debugMode = false
    ) {
        $this->stylesheets = &$stylesheets;
        $this->javascripts = &$javascripts;
        $this->head        = &$head;
        $this->body        = &$body;
        $this->debugMode   = $debugMode;
    }

    /**
     * {@inheritDoc}
     */
    public function addJavascript(string $path, $static = self::STATIC_PRODUCTION, ?string $name = null): AssetsManager
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
    public function addJavascripts(array $paths, $static = self::STATIC_PRODUCTION, ?string $name = null): AssetsManager
    {
        foreach ($paths as $identifier => $path) {
            if ($name) {
                $name .= '_' . $identifier;
            } elseif (! is_numeric($identifier)) {
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
        ?string $name = null
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
        ?string $name = null
    ): AssetsManager {
        foreach ($paths as $identifier => $path) {
            if ($name) {
                $name .= '_' . $identifier;
            } elseif (! is_numeric($identifier)) {
                $name = $identifier;
            }

            $this->addStylesheet($path, $media, $static, $name);
        }

        return $this;
    }

    public function addToBody(string $name, string $html): HtmlPageAssetsManager
    {
        $this->body[$name] = $html;

        return $this;
    }

    public function appendToBody(string $html): HtmlPageAssetsManager
    {
        $this->body[] = $html;

        return $this;
    }

    public function addToHead(string $name, string $html): HtmlPageAssetsManager
    {
        $this->head[$name] = $html;

        return $this;
    }

    public function appendToHead(string $html): HtmlPageAssetsManager
    {
        $this->head[] = $html;

        return $this;
    }

    /**
     * Get javascripts.
     *
     * @return array<int|string,string>
     */
    public function getJavascripts(): array
    {
        return $this->javascripts;
    }

    /**
     * Get stylesheets.
     *
     * @return array<int|string,string>
     */
    public function getStylesheets(): array
    {
        return $this->stylesheets;
    }

    /**
     * Get body.
     *
     * @return array|string[]
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Get head.
     *
     * @return array|string[]
     */
    public function getHead(): array
    {
        return $this->head;
    }

    /**
     * @param string|bool $flag
     */
    private function isStatic($flag): bool
    {
        if ($flag === self::STATIC_PRODUCTION) {
            return ! $this->debugMode;
        }

        return (bool) $flag;
    }
}
