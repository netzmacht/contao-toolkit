<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

use Symfony\Component\Asset\Packages;

use function is_array;

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
        foreach (['TL_CSS', 'TL_JAVASCRIPT', 'TL_HEAD', 'TL_BODY'] as $key) {
            if (isset($GLOBALS[$key]) && is_array($GLOBALS[$key])) {
                continue;
            }

            $GLOBALS[$key] = [];
        }

        return new GlobalsAssetsManager(
            $this->packages,
            $GLOBALS['TL_CSS'],
            $GLOBALS['TL_JAVASCRIPT'],
            $GLOBALS['TL_HEAD'],
            $GLOBALS['TL_BODY'],
            $this->debug,
        );
    }
}
