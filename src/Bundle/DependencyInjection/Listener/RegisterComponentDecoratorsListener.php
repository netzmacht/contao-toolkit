<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Listener;

use Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElementDecorator;
use Netzmacht\Contao\Toolkit\Component\Module\ModuleDecorator;

final class RegisterComponentDecoratorsListener
{
    /**
     * List of modules in their categories.
     *
     * @var array<string,list<string>>
     */
    private $modules;

    /**
     * List of elements in their categories.
     *
     * @var array<string,list<string>>
     */
    private $elements;

    /**
     * @param array<string,list<string>> $modules  List of modules in their categories.
     * @param array<string,list<string>> $elements List of elements in their categories.
     */
    public function __construct(array $modules, array $elements)
    {
        $this->modules  = $modules;
        $this->elements = $elements;
    }

    /**
     * Handle the on initialize system hook.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @psalm-suppress DeprecatedClass
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
