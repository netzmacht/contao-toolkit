<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FormatterChain;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FormatterChainSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin FormatterChain
 */
class FormatterChainSpec extends ObjectBehavior
{
    function let(ValueFormatter $formatterA, ValueFormatter $formatterB)
    {
        $this->beConstructedWith([$formatterA, $formatterB]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FormatterChain');
    }

    function it_accepts_if_one_child_does(ValueFormatter $formatterA, ValueFormatter $formatterB)
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterB->accepts(Argument::cetera())->willReturn(true);

        $this->accepts('test', [])->shouldReturn(true);
    }

    function it_does_not_accept_if_none_child_does(ValueFormatter $formatterA, ValueFormatter $formatterB)
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterB->accepts(Argument::cetera())->willReturn(false);

        $this->accepts('test', [])->shouldReturn(false);
    }

    function it_applies_matching_formatter(ValueFormatter $formatterA, ValueFormatter $formatterB)
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterA->format(Argument::cetera())->shouldNotBeCalled();

        $formatterB->accepts(Argument::cetera())->willReturn(true);
        $formatterB->format(Argument::cetera())->willReturn('bar')->shouldBeCalled();

        $this->format('foo', 'test', [])->shouldReturn('bar');
    }

    function it_applies_first_matching_formatter(ValueFormatter $formatterA, ValueFormatter $formatterB)
    {
        $formatterA->accepts(Argument::cetera())->willReturn(true);
        $formatterA->format(Argument::cetera())->willReturn('bar')->shouldBeCalled();

        $formatterB->accepts(Argument::cetera())->willReturn(true);
        $formatterB->format(Argument::cetera())->shouldNotBeCalled();


        $this->format('foo', 'test', [])->shouldReturn('bar');
    }

    function it_returns_initial_value_when_no_formatter_matches(ValueFormatter $formatterA, ValueFormatter $formatterB)
    {
        $formatterA->accepts(Argument::cetera())->willReturn(false);
        $formatterB->accepts(Argument::cetera())->willReturn(false);

        $this->format('foo', 'test', [])->shouldReturn('foo');
    }
}
