<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HiddenValueFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class HiddenValueFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin HiddenValueFormatter
 */
class HiddenValueFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HiddenValueFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_password_field()
    {
        $definition['inputType'] = 'password';

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_accepts_do_not_show_option()
    {
        $definition['eval']['doNotShow'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_accepts_hide_input_option()
    {
        $definition['eval']['hideInput'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_hides_value()
    {
        $this->format('test', 'test', [])->shouldReturn('');
    }

    function it_has_a_password_mask_option_for_password_fields()
    {
        $this->beConstructedWith('*****');
        $definition['inputType'] = 'password';

        $this->format('test', 'test', $definition)->shouldReturn('*****');
    }

    function it_ignores_the_password_mask_option_for_non_password_fields()
    {
        $this->beConstructedWith('*****');
        $definition['inputType'] = 'text';

        $this->format('test', 'test', $definition)->shouldReturn('');
    }
}
