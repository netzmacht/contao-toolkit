<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use PhpSpec\ObjectBehavior;

use function serialize;

class ComponentDecoratorTraitSpec extends ObjectBehavior
{
    /** @var Model */
    private $model;

    /** @var array<string,mixed> */
    private $modelData;

    public function let(ComponentFactory $componentFactory, Component $component): void
    {
        $this->modelData = [
            'type'      => 'test',
            'headline'  => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'        => 1,
            'customTpl' => 'custom_tpl',
            'cssID'     => serialize(['', '']),
        ];

        $this->model = new class ($this->modelData) extends Model {
            /**
             * @param array<string,mixed> $data Model data.
             */
            public function __construct(array $data)
            {
                $this->arrData = $data;
            }
        };

        $componentFactory->create($this->model, 'main')->willReturn($component);

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\ComponentDecorator');
        $this->beConstructedWith($component, $componentFactory, $this->model, 'main');
    }

    public function it_delegates_get(Component $component): void
    {
        $component->get('foo')->shouldBeCalled();
        $this->__get('foo');
    }

    public function it_delegates_set(Component $component): void
    {
        $component->set('foo', 'bar')->shouldBeCalled();
        $this->__set('foo', 'bar');
    }

    public function it_delegates_has(Component $component): void
    {
        $component->has('foo')->shouldBeCalled();
        $this->__isset('foo');
    }

    public function it_delegates_get_model(Component $component): void
    {
        $component->getModel()->shouldBeCalled();
        $this->getModel('foo');
    }

    public function it_delegates_generate(Component $component): void
    {
        $component->generate()->shouldBeCalled();
        $this->generate();
    }
}
