<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FilterFormatterSpec extends ObjectBehavior
{
    public function let(ValueFormatter $formatterA, ValueFormatter $formatterB): void
    {
        $this->beConstructedWith([$formatterA, $formatterB]);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FilterFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_anything(): void
    {
        $this->accepts('test', [])->shouldReturn(true);
    }

    public function it_applies_matching_formatters(ValueFormatter $formatterA, ValueFormatter $formatterB): void
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterA->format(Argument::cetera())->shouldNotBeCalled();

        $formatterB->accepts(Argument::cetera())->willReturn(true);
        $formatterB->format(Argument::cetera())->shouldBeCalled()->willReturn('bar');

        $this->format('foo', 'test', [])->shouldReturn('bar');
    }
}
