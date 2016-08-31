<?php

namespace spec\Netzmacht\Contao\Toolkit\Boot\Event;

use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Toolkit\Boot\Event\InitializeSystemEvent;
use PhpSpec\ObjectBehavior;

/**
 * Class InitializeSystemEventSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Boot\Event
 * @mixin InitializeSystemEvent
 */
class InitializeSystemEventSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);

    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Boot\Event\InitializeSystemEvent');
    }

    function it_is_a_symfony_event()
    {
        $this->shouldHaveType('Symfony\Component\EventDispatcher\Event');
    }

    function it_stores_the_container(ContainerInterface $container)
    {
        $this->getContainer()->shouldReturn($container);
    }
}
