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

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FilterFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FilterFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin FilterFormatter
 */
class FilterFormatterSpec extends ObjectBehavior
{
    function let(ValueFormatter $formatterA, ValueFormatter $formatterB)
    {
        $this->beConstructedWith([$formatterA, $formatterB]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FilterFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_anything()
    {
        $this->accepts('test', [])->shouldReturn(true);
    }

    function it_applies_matching_formatters(ValueFormatter $formatterA, ValueFormatter $formatterB)
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterA->format(Argument::cetera())->shouldNotBeCalled();

        $formatterB->accepts(Argument::cetera())->willReturn(true);
        $formatterB->format(Argument::cetera())->shouldBeCalled()->willReturn('bar');

        $this->format('foo', 'test', [])->shouldReturn('bar');
    }
}
