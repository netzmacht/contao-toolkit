<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DeserializeFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class DeserializeFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin DeserializeFormatter
 */
class DeserializeFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DeserializeFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_everything()
    {
        $this->accepts(uniqid(), [])->shouldReturn(true);
    }

    function it_deserializes_arrays()
    {
        $value = ['test' => 'foo'];
        $raw   = serialize($value);

        $this->format($raw, 'test', [])->shouldReturn($value);
    }

    function it_returns_scalar_values()
    {
        foreach ([true, false, null, 5, 5.6, 'foo'] as $value) {
            $this->format($value, 'test', [])->shouldReturn($value);
        }
    }
}
