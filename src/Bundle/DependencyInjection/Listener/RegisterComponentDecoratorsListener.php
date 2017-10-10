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

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Listener;

use Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElementDecorator;
use Netzmacht\Contao\Toolkit\Component\Module\ModuleDecorator;

/**
 * Class RegisterComponentDecoratorsListener.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\Listener
 */
final class RegisterComponentDecoratorsListener
{
    /**
     * List of modules in their categories.
     *
     * @var array
     */
    private $modules;

    /**
     * List of elements in their categories.
     *
     * @var array
     */
    private $elements;

    /**
     * RegisterComponentDecoratorsListener constructor.
     *
     * @param array $modules  List of modules in their categories.
     * @param array $elements List of elements in their categories.
     */
    public function __construct(array $modules, array $elements)
    {
        $this->modules  = $modules;
        $this->elements = $elements;
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
        foreach ($this->elements as $category => $elements) {
            foreach ($elements as $element) {
                $GLOBALS['TL_CTE'][$category][$element] = ContentElementDecorator::class;
            }
        }

        foreach ($this->modules as $category => $modules) {
            foreach ($modules as $module) {
                $GLOBALS['FE_MOD'][$category][$module] = ModuleDecorator::class;
            }
        }
    }
}
