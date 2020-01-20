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

    function it_does_not_accept_a_field_by_default()
    {
        $this->accepts('test', [])->shouldReturn(false);
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
