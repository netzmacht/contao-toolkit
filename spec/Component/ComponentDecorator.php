<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Contao\Module;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;

class ComponentDecorator extends Module
{
    use ComponentDecoratorTrait;

    /** @var ComponentFactory */
    private $factory;

    /**
     * @param Component        $component The component.
     * @param ComponentFactory $factory   The component factory.
     */
    public function __construct(Component $component, $factory)
    {
        $this->component = $component;
        $this->factory   = $factory;
    }

    /**
     * Get the component factory.
     */
    protected function getFactory(): ComponentFactory
    {
        return $this->factory;
    }
}
