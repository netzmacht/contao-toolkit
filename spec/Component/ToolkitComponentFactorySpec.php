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
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ToolkitComponentFactorySpec extends ObjectBehavior
{
    /** @var Component */
    private $example;

    public function let(ComponentFactory $factory)
    {
        $this->example = new class() implements Component {
            public function set(string $name, $value): Component
            {
                return $this;
            }

            public function get(string $name)
            {
            }

            public function has(string $name): bool
            {
                return true;
            }

            public function getModel()
            {
            }

            public function generate(): string
            {
                return '';
            }
        };

        $this->beConstructedWith([$factory]);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ComponentFactory::class);
    }

    public function it_should_throw_component_not_found_for_unknown_types(ComponentFactory $factory)
    {
        $model = (object) ['type' => 'unknown', 'id' => '4'];
        $factory->supports($model)->willReturn(false);
        $this
            ->shouldThrow(ComponentNotFound::class)
            ->duringCreate($model, 'main');
    }

    public function it_should_throw_component_not_found_for_created_non_components(ComponentFactory $factory)
    {
        $model = (object) ['type' => 'invalid', 'id' => '4'];
        $factory->supports($model)->willReturn(false);

        $this
            ->shouldThrow(ComponentNotFound::class)
            ->duringCreate($model, 'main');
    }

    public function it_creates_component_calling_responsible_factory(ComponentFactory $factory, Model $model)
    {
        $factory->supports($model)->willReturn(true);
        $factory->create($model, 'main')->willReturn($this->example)->shouldBeCalled();

        $this->create($model, 'main')->shouldReturn($this->example);
    }
}
