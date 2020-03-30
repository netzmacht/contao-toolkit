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

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ReferenceFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ReferenceFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin ReferenceFormatter
 */
class ReferenceFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ReferenceFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_definitions_with_non_empty_reference()
    {
        $definition['reference'] = ['test' => 'foo'];

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_does_not_accept_definitions_with_empty_reference()
    {
        $definition['reference'] = [];

        $this->accepts('test', $definition)->shouldReturn(false);
    }

    function it_does_not_accept_definitions_without_reference()
    {
        $definition = [];

        $this->accepts('test', $definition)->shouldReturn(false);
    }

    function it_formats_value_by_look_up_reference()
    {
        $definition['reference'] = ['bar' => 'foo'];

        $this->format('bar', 'test', $definition)->shouldReturn('foo');
    }

    function it_takes_indexed_null_item_of_array_reference()
    {
        $definition['reference'] = ['bar' => [1 => 'foobar', 0 => 'foo']];

        $this->format('bar', 'test', $definition)->shouldReturn('foo');
    }
}
