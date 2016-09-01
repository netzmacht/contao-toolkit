<?php

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ToolkitComponentFactory;
use PhpSpec\ObjectBehavior;

/**
 * Class ComponentFactorySpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Component
 * @mixin ToolkitComponentFactory
 */
class ToolkitComponentFactorySpec extends ObjectBehavior
{
    private $factories;

    private $example;

    function let(ContainerInterface $container)
    {
        $this->example   = new ComponentExample();
        $this->factories = new \ArrayObject(
            [
                'invalid' => function () {
                    return ['foo'];
                },
                'known' => function () {
                    return $this->example;
                }
            ]
        );

        $this->beConstructedWith($this->factories, $container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Component\ComponentFactory');
    }

    function it_should_throw_component_not_found_for_unknown_types()
    {
        $model = (object) ['type' => 'unknown', 'id' => '4'];
        $this
            ->shouldThrow('Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound')
            ->duringCreate($model, 'main');
    }

    function it_should_throw_component_not_found_for_created_non_components()
    {
        $model = (object) ['type' => 'invalid', 'id' => '4'];
        $this
            ->shouldThrow('Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound')
            ->duringCreate($model, 'main');
    }

    function it_creates_component_calling_responsible_factory()
    {
        $model = (object) ['type' => 'known', 'id' => '4'];
        $this->create($model, 'main')->shouldReturn($this->example);
    }
}

class ComponentExample implements Component
{
    public function set($name, $value)
    {
    }

    public function get($name)
    {
    }

    public function has($name)
    {
    }

    public function getModel()
    {
    }

    public function generate()
    {
    }
}
