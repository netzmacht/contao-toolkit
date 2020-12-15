<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

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
     * ComponentDecorator constructor.
     *
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
     *
     * @return ComponentFactory
     */
    protected function getFactory()
    {
        return $this->factory;
    }
}
