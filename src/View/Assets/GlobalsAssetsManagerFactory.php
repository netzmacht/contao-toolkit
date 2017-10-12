<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Assets;

/**
 * Class GlobalsAssetsManagerFactory creates the global assets manager.
 *
 * @package Netzmacht\Contao\Toolkit\View\Assets
 */
final class GlobalsAssetsManagerFactory
{
    /**
     * Kernel debug mode.
     *
     * @var bool
     */
    private $debug;

    /**
     * Web directory.
     *
     * @var string
     */
    private $webDir;

    /**
     * Root directory.
     *
     * @var string
     */
    private $rootDir;

    /**
     * GlobalsAssetsManagerFactory constructor.
     *
     * @param bool   $debug      Debug mode.
     * @param string $projectDir Kernel project directory.
     * @param string $webDir     Web directory.
     */
    public function __construct(bool $debug, string $projectDir, string $webDir)
    {
        $this->debug   = $debug;
        $this->webDir  = $webDir;
        $this->rootDir = $projectDir;
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
            substr($this->webDir, (strlen($this->rootDir) + 1)),
            $this->debug
        );
    }
}
