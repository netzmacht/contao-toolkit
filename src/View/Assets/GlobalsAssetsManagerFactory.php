<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

use Symfony\Component\Asset\Packages;

/**
 * Class GlobalsAssetsManagerFactory creates the global assets manager.
 */
final class GlobalsAssetsManagerFactory
{
    /** @param bool $debug Debug mode. */
    public function __construct(private readonly Packages $packages, private readonly bool $debug)
    {
    }

    /**
     * Create the assets manager.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function create(): AssetsManager
    {
        return new GlobalsAssetsManager($this->packages, $this->debug);
    }
}
