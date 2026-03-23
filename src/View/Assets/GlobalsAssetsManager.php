<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

use Override;
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
     * Debug mode of the environment.
     */
    private bool $debugMode;

    /** @param bool $debugMode Debug mode of the environment. */
    public function __construct(
        private readonly Packages $packages,
        bool $debugMode = false,
    ) {
        $this->debugMode = $debugMode;
    }

    /** {@inheritDoc} */
    #[Override]
    public function addJavascript(
        string $path,
        bool|string $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        $path = $this->locatePath($path);

        if ($this->isStatic($static)) {
            $path .= '|static';
        }

        $this->addTo('TL_JAVASCRIPT', $path, $name);

        return $this;
    }

    /** {@inheritDoc} */
    #[Override]
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
    #[Override]
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

        $this->addTo('TL_CSS', $path, $name);

        return $this;
    }

    /** {@inheritDoc} */
    #[Override]
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

    #[Override]
    public function addToBody(string $name, string $html): AssetsManager
    {
        $this->addTo('TL_BODY', $html, $name);

        return $this;
    }

    #[Override]
    public function appendToBody(string $html): AssetsManager
    {
        $this->addTo('TL_BODY', $html);

        return $this;
    }

    #[Override]
    public function addToHead(string $name, string $html): AssetsManager
    {
        $this->addTo('TL_HEAD', $html, $name);

        return $this;
    }

    #[Override]
    public function appendToHead(string $html): AssetsManager
    {
        $this->addTo('TL_HEAD', $html);

        return $this;
    }

    private function addTo(string $key, string $value, string|null $name = null): void
    {
        if (! isset($GLOBALS[$key])) {
            $GLOBALS[$key] = [];
        }

        if ($name === null) {
            $GLOBALS[$key][] = $value;
        } else {
            $GLOBALS[$key][$name] = $value;
        }
    }

    /**
     * Get javascripts.
     *
     * @return array<int|string,string>
     */
    public function getJavascripts(): array
    {
        return $GLOBALS['TL_JAVASCRIPT'] ?? [];
    }

    /**
     * Get stylesheets.
     *
     * @return array<int|string,string>
     */
    public function getStylesheets(): array
    {
        return $GLOBALS['TL_CSS'] ?? [];
    }

    /**
     * Get body.
     *
     * @return array|string[]
     */
    public function getBody(): array
    {
        return $GLOBALS['TL_BODY'] ?? [];
    }

    /**
     * Get head.
     *
     * @return array|string[]
     */
    public function getHead(): array
    {
        return $GLOBALS['TL_HEAD'] ?? [];
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
