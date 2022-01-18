<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FormatterChainSpec extends ObjectBehavior
{
    public function let(ValueFormatter $formatterA, ValueFormatter $formatterB): void
    {
        $this->beConstructedWith([$formatterA, $formatterB]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FormatterChain');
    }

    public function it_accepts_if_one_child_does(ValueFormatter $formatterA, ValueFormatter $formatterB): void
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterB->accepts(Argument::cetera())->willReturn(true);

        $this->accepts('test', [])->shouldReturn(true);
    }

    public function it_does_not_accept_if_none_child_does(ValueFormatter $formatterA, ValueFormatter $formatterB): void
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterB->accepts(Argument::cetera())->willReturn(false);

        $this->accepts('test', [])->shouldReturn(false);
    }

    public function it_applies_matching_formatter(ValueFormatter $formatterA, ValueFormatter $formatterB): void
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterA->format(Argument::cetera())->shouldNotBeCalled();

        $formatterB->accepts(Argument::cetera())->willReturn(true);
        $formatterB->format(Argument::cetera())->willReturn('bar')->shouldBeCalled();

        $this->format('foo', 'test', [])->shouldReturn('bar');
    }

    public function it_applies_first_matching_formatter(ValueFormatter $formatterA, ValueFormatter $formatterB): void
    {
        $formatterA->accepts(Argument::cetera())->willReturn(true);
        $formatterA->format(Argument::cetera())->willReturn('bar')->shouldBeCalled();

        $formatterB->accepts(Argument::cetera())->willReturn(true);
        $formatterB->format(Argument::cetera())->shouldNotBeCalled();

        $this->format('foo', 'test', [])->shouldReturn('bar');
    }

    public function it_returns_initial_value_when_no_formatter_matches(
        ValueFormatter $formatterA,
        ValueFormatter $formatterB
    ): void {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterB->accepts(Argument::cetera())->willReturn(false);

        $this->format('foo', 'test', [])->shouldReturn('foo');
    }
}
