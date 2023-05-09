<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

use Symfony\Component\Asset\Packages;

use function array_map;
use function count;
use function explode;
use function strpos;

/**
 * This class will locate an asset from the asset package component.
 *
 * The package name has to be prefixed separated by double colons: package_name::asset.css
 */
final class AssetPackageAssetsManager implements AssetsManager
{
    public function __construct(private AssetsManager $assetsManager, private Packages $packages)
    {
    }

    /** {@inheritDoc} */
    public function addJavascript(
        string $path,
        $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        $this->assetsManager->addJavascript($this->locatePath($path), $static, $name);

        return $this;
    }

    /** {@inheritDoc} */
    public function addJavascripts(
        array $paths,
        $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        $paths = array_map([$this, 'locatePath'], $paths);
        $this->assetsManager->addJavascripts($paths, $static, $name);

        return $this;
    }

    /** {@inheritDoc} */
    public function addStylesheet(
        string $path,
        string $media = '',
        $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        $this->assetsManager->addStylesheet($this->locatePath($path), $media, $static, $name);

        return $this;
    }

    /** {@inheritDoc} */
    public function addStylesheets(
        array $paths,
        string $media = '',
        $static = self::STATIC_PRODUCTION,
        string|null $name = null,
    ): AssetsManager {
        $paths = array_map([$this, 'locatePath'], $paths);
        $this->assetsManager->addStylesheets($paths, $media, $static, $name);

        return $this;
    }

    /** {@inheritDoc} */
    public function addToBody(string $name, string $html): AssetsManager
    {
        $this->assetsManager->addToBody($name, $html);

        return $this;
    }

    /** {@inheritDoc} */
    public function appendToBody(string $html): AssetsManager
    {
        $this->assetsManager->appendToBody($html);

        return $this;
    }

    /** {@inheritDoc} */
    public function addToHead(string $name, string $html): AssetsManager
    {
        $this->assetsManager->addToHead($name, $html);

        return $this;
    }

    /** {@inheritDoc} */
    public function appendToHead(string $html): AssetsManager
    {
        $this->assetsManager->appendToHead($html);

        return $this;
    }

    private function locatePath(string $path): string
    {
        if (strpos($path, '::') === false) {
            return $path;
        }

        $parts = explode('::', $path, 2);
        if (count($parts) === 1) {
            return $path;
        }

        return $this->packages->getUrl($parts[1], $parts[0]);
    }
}
