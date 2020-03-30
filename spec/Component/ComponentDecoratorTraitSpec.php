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

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use PhpSpec\ObjectBehavior;
use function serialize;

/**
 * Class ComponentDecoratorTraitSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Component
 * @mixin \Module
 */
class ComponentDecoratorTraitSpec extends ObjectBehavior
{
    private $model;

    private $modelData;

    function let(ComponentFactory $componentFactory, Component $component)
    {
        $this->modelData = [
            'type' => 'test',
            'headline' => serialize(['unit' => 'h1', 'value' => 'test']),
            'id' => 1,
            'customTpl' => 'custom_tpl',
            'cssID' => serialize(['', ''])
        ];

        $this->model = new Model($this->modelData);

        $componentFactory->create($this->model, 'main')->willReturn($component);

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\ComponentDecorator');
        $this->beConstructedWith($component, $componentFactory, $this->model, 'main');
    }

    function it_delegates_get(Component $component)
    {
        $component->get('foo')->shouldBeCalled();
        $this->__get('foo');
    }

    function it_delegates_set(Component $component)
    {
        $component->set('foo', 'bar')->shouldBeCalled();
        $this->__set('foo', 'bar');
    }

    function it_delegates_has(Component $component)
    {
        $component->has('foo')->shouldBeCalled();
        $this->__isset('foo');
    }

    function it_delegates_get_model(Component $component)
    {
        $component->getModel()->shouldBeCalled();
        $this->getModel('foo');
    }

    function it_delegates_generate(Component $component)
    {
        $component->generate()->shouldBeCalled();
        $this->generate();
    }

}

class ComponentDecorator extends \Contao\Module
{
    use ComponentDecoratorTrait;

    private $factory;

    public function __construct(Component $component, $factory)
    {
        $this->component = $component;
    }

    protected function getFactory()
    {
        return $this->factory;
    }
}
