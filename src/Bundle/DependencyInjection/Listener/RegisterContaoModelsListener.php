<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     Christopher Bölter <christopher@boelter.eu>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2019 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Listener;

/**
 * Class RegisterContaoModelsListener.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\Listener
 */
final class RegisterContaoModelsListener
{
    /**
     * List of repositories.
     *
     * @var array
     */
    private $repositories;

    /**
     * RegisterContaoModelsListener constructor.
     *
     * @param array $repositories List of repositories.
     */
    public function __construct(array $repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Handle the on initialize system hook.
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function onInitializeSystem(): void
    {
        foreach ($this->repositories as $table => $modelClass) {
            $GLOBALS['TL_MODELS'][$table] = $modelClass;
        }
    }
}
