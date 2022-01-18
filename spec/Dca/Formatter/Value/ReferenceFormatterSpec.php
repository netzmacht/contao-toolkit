<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use PhpSpec\ObjectBehavior;

class ReferenceFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ReferenceFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_definitions_with_non_empty_reference(): void
    {
        $definition['reference'] = ['test' => 'foo'];

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_definitions_with_empty_reference(): void
    {
        $definition['reference'] = [];

        $this->accepts('test', $definition)->shouldReturn(false);
    }

    public function it_does_not_accept_definitions_without_reference(): void
    {
        $definition = [];

        $this->accepts('test', $definition)->shouldReturn(false);
    }

    public function it_formats_value_by_look_up_reference(): void
    {
        $definition['reference'] = ['bar' => 'foo'];

        $this->format('bar', 'test', $definition)->shouldReturn('foo');
    }

    public function it_takes_indexed_null_item_of_array_reference(): void
    {
        $definition['reference'] = ['bar' => [1 => 'foobar', 0 => 'foo']];

        $this->format('bar', 'test', $definition)->shouldReturn('foo');
    }
}
