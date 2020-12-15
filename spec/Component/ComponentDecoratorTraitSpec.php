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
use PhpSpec\ObjectBehavior;
use function serialize;

/**
 * Class ComponentDecoratorTraitSpec
 */
class ComponentDecoratorTraitSpec extends ObjectBehavior
{
    /** @var \Contao\Model */
    private $model;

    /** @var array */
    private $modelData;

    public function let(ComponentFactory $componentFactory, Component $component)
    {
        $this->modelData = [
            'type'      => 'test',
            'headline'  => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'        => 1,
            'customTpl' => 'custom_tpl',
            'cssID'     => serialize(['', ''])
        ];

        $this->model = new class($this->modelData) extends Model {
            /**
             * Model constructor.
             *
             * @param array $data Model data.
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

    public function it_delegates_get(Component $component)
    {
        $component->get('foo')->shouldBeCalled();
        $this->__get('foo');
    }

    public function it_delegates_set(Component $component)
    {
        $component->set('foo', 'bar')->shouldBeCalled();
        $this->__set('foo', 'bar');
    }

    public function it_delegates_has(Component $component)
    {
        $component->has('foo')->shouldBeCalled();
        $this->__isset('foo');
    }

    public function it_delegates_get_model(Component $component)
    {
        $component->getModel()->shouldBeCalled();
        $this->getModel('foo');
    }

    public function it_delegates_generate(Component $component)
    {
        $component->generate()->shouldBeCalled();
        $this->generate();
    }
}
