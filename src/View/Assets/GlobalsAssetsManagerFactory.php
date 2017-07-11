<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Assets;

use Symfony\Component\HttpKernel\KernelInterface as HttpKernel;

/**
 * Class GlobalsAssetsManagerFactory creates the global assets manager.
 *
 * @package Netzmacht\Contao\Toolkit\View\Assets
 */
class GlobalsAssetsManagerFactory
{
    /**
     * Http kernel.
     *
     * @var HttpKernel
     */
    private $kernel;

    /**
     * GlobalsAssetsManagerFactory constructor.
     *
     * @param HttpKernel $kernel Http kernel.
     */
    public function __construct(HttpKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Create the assets manager.
     *
     * @return AssetsManager
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function create()
    {
        if (!isset($GLOBALS['TL_CSS']) || !is_array($GLOBALS['TL_CSS'])) {
            $GLOBALS['TL_CSS'] = [];
        }

        if (!isset($GLOBALS['TL_JAVASCRIPT']) || !is_array($GLOBALS['TL_JAVASCRIPT'])) {
            $GLOBALS['TL_JAVASCRIPT'] = [];
        }

        return new GlobalsAssetsManager(
            $GLOBALS['TL_CSS'],
            $GLOBALS['TL_JAVASCRIPT'],
            $this->kernel->isDebug()
        );
    }
}
