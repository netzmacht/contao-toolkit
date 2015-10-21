<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FlattenFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FlattenFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin FlattenFormatter
 */
class FlattenFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FlattenFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_multiple_fields()
    {
        $definition['eval']['multiple'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_does_not_accept_non_multiple_fields()
    {
        $definition['eval']['multiple'] = false;
        $this->accepts('test', $definition)->shouldReturn(false);
        $this->accepts('test', [])->shouldReturn(false);
    }

    function it_does_not_accept_a_field_by_default()
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    function it_flattens_arrays_to_csv()
    {
        $value = ['a', 'b'];

        $this->format($value, 'test', [])->shouldReturn('a, b');
    }

    function it_flattens_nested_arrays()
    {
        $value = ['a', ['b', 'c']];

        $this->format($value, 'test', [])->shouldReturn('a, [b, c]');
    }

    function it_flattens_objects()
    {
        $value = (object) ['a' => 'a', 'b' => ['b', 'c']];

        $this->format($value, 'test', [])->shouldReturn('a, [b, c]');
    }

    function it_bypasses_scalar_values()
    {
        foreach (['a', 1.3, 5, true, false, null] as $value) {
            $this->format($value, 'test', [])->shouldReturn($value);
        }
    }
}
