<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

use Symfony\Component\Asset\Packages;

use function count;
use function explode;
use function is_numeric;
use function str_contains;

/**
 * Globals assets manager registers all assets as Contao globals.
 */
final class GlobalsAssetsManager implements AssetsManager
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
        private readonly Packages $packages,
        array &$stylesheets,
        array &$javascripts,
        array &$head,
        array &$body,
        bool $debugMode = false,
    ) {
        $this->stylesheets = &$stylesheets;
        $this->javascripts = &$javascripts;
        $this->head        = &$head;
        $this->body        = &$body;
        $this->debugMode   = $debugMode;
    }

    /** {@inheritDoc} */
    public function addJavascript(
        string $path,
        bool|string $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        $path = $this->locatePath($path);

        if ($this->isStatic($static)) {
            $path .= '|static';
        }

        if ($name === null) {
            $this->javascripts[] = $path;
        } else {
            $this->javascripts[$name] = $path;
        }

        return $this;
    }

    /** {@inheritDoc} */
    public function addJavascripts(
        array $paths,
        bool|string $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        foreach ($paths as $identifier => $path) {
            if ($name !== null && $name !== '') {
                $name .= '_' . $identifier;
            } elseif (! is_numeric($identifier)) {
                $name = $identifier;
            }

            $this->addJavascript($path, $static, $name);
        }

        return $this;
    }

    /** {@inheritDoc} */
    public function addStylesheet(
        string $path,
        string $media = '',
        bool|string $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        $path   = $this->locatePath($path);
        $static = $this->isStatic($static);

        if ($media || $static) {
            $path .= '|' . $media;

            if ($static) {
                $path .= '|static';
            }
        }

        if ($name === null) {
            $this->stylesheets[] = $path;
        } else {
            $this->stylesheets[$name] = $path;
        }

        return $this;
    }

    /** {@inheritDoc} */
    public function addStylesheets(
        array $paths,
        string $media = '',
        bool|string $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        foreach ($paths as $identifier => $path) {
            if ($name !== null && $name !== '') {
                $name .= '_' . $identifier;
            } elseif (! is_numeric($identifier)) {
                $name = $identifier;
            }

            $this->addStylesheet($path, $media, $static, $name);
        }

        return $this;
    }

    public function addToBody(string $name, string $html): AssetsManager
    {
        $this->body[$name] = $html;

        return $this;
    }

    public function appendToBody(string $html): AssetsManager
    {
        $this->body[] = $html;

        return $this;
    }

    public function addToHead(string $name, string $html): AssetsManager
    {
        $this->head[$name] = $html;

        return $this;
    }

    public function appendToHead(string $html): AssetsManager
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

    private function isStatic(bool|string $flag): bool
    {
        if ($flag === self::STATIC_PRODUCTION) {
            return ! $this->debugMode;
        }

        return (bool) $flag;
    }

    private function locatePath(string $path): string
    {
        if (! str_contains($path, '::')) {
            return $path;
        }

        $parts = explode('::', $path, 2);
        if (count($parts) === 1) {
            return $path;
        }

        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        return $this->packages->getUrl($parts[1], $parts[0]);
    }
}
