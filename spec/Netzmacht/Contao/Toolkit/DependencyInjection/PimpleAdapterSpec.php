<?php

namespace spec\Netzmacht\Contao\Toolkit\DependencyInjection;

use Netzmacht\Contao\Toolkit\DependencyInjection\PimpleAdapter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PimpleAdapterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\DependencyInjection
 * @mixin PimpleAdapter
 */
class PimpleAdapterSpec extends ObjectBehavior
{
    function let(\Pimple $pimple)
    {
        $this->beConstructedWith($pimple);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\DependencyInjection\PimpleAdapter');
    }

    function it_is_a_container()
    {
        $this->shouldImplement('Interop\Container\ContainerInterface');
    }

    function it_delegates_get(\Pimple $pimple)
    {
        $pimple->offsetGet('foo')->shouldBeCalled();
        $this->get('foo');
    }

    function it_delegates_has(\Pimple $pimple)
    {
        $pimple->offsetExists('foo')->shouldBeCalled();
        $this->has('foo');
    }
}
