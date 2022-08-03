<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

use function is_array;

/**
 * Class GlobalsAssetsManagerFactory creates the global assets manager.
 */
final class GlobalsAssetsManagerFactory
{
    /**
     * Kernel debug mode.
     */
    private bool $debug;

    /**
     * @param bool $debug Debug mode.
     */
    public function __construct(bool $debug)
    {
        $this->debug = $debug;
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
            $GLOBALS['TL_CSS'],
            $GLOBALS['TL_JAVASCRIPT'],
            $GLOBALS['TL_HEAD'],
            $GLOBALS['TL_BODY'],
            $this->debug
        );
    }
}
