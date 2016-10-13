<?php

namespace spec\Netzmacht\Contao\Toolkit\View\Template\Subscriber;

use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use Netzmacht\Contao\Toolkit\View\Template\Subscriber\GetTemplateHelpersListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class GetTemplateHelpersListenerSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\View\Template\Subscriber
 * @mixin GetTemplateHelpersListener
 */
class GetTemplateHelpersListenerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container)
    {
        $this->beConstructedWith($container);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\View\Template\Subscriber\GetTemplateHelpersListener');
    }

    function it_registers_default_helpers(GetTemplateHelpersEvent $event)
    {
        $event->addHelper('assets', Argument::any())->shouldBeCalled()->willReturn($event);
        $event->addHelper('translator', Argument::any())->shouldBeCalled()->willReturn($event);

        $this->handle($event);
    }
}
