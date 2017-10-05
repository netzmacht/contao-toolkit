<?php

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;
use Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound;
use Netzmacht\Contao\Toolkit\Component\ToolkitComponentFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ComponentFactorySpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Component
 * @mixin ToolkitComponentFactory
 */
class ToolkitComponentFactorySpec extends ObjectBehavior
{
    private $example;

    function let(ComponentFactory $factory)
    {
        $this->example = new ComponentExample();

        $this->beConstructedWith([$factory]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ComponentFactory::class);
    }

    function it_should_throw_component_not_found_for_unknown_types(ComponentFactory $factory)
    {
        $model = (object) ['type' => 'unknown', 'id' => '4'];
        $factory->supports($model)->willReturn(false);
        $this
            ->shouldThrow(ComponentNotFound::class)
            ->duringCreate($model, 'main');
    }

    function it_should_throw_component_not_found_for_created_non_components(ComponentFactory $factory)
    {
        $model = (object) ['type' => 'invalid', 'id' => '4'];
        $factory->supports($model)->willReturn(false);

        $this
            ->shouldThrow(ComponentNotFound::class)
            ->duringCreate($model, 'main');
    }

    function it_creates_component_calling_responsible_factory(ComponentFactory $factory, Model $model)
    {
        $factory->supports($model)->willReturn(true);
        $factory->create($model, 'main')->willReturn($this->example)->shouldBeCalled();

        $this->create($model, 'main')->shouldReturn($this->example);
    }
}

class ComponentExample implements Component
{
    public function set(string $name, $value): Component
    {
        return $this;
    }

    public function get(string $name)
    {
    }

    public function has(string $name): bool
    {
    }

    public function getModel(): ?Model
    {
    }

    public function generate(): string
    {
    }
}
