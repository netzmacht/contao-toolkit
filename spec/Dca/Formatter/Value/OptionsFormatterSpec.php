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

use Contao\CoreBundle\Framework\Adapter;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\OptionsFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class OptionsFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin OptionsFormatter
 */
class OptionsFormatterSpec extends ObjectBehavior
{
    function let(Adapter $adapter)
    {
        $this->beConstructedWith(new Invoker($adapter));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\OptionsFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_fields_with_options()
    {
        $definition['options'] = [
            'foo' => 'bar'
        ];

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_accepts_fields_with_is_associative_flag()
    {
        $definition['eval']['isAssociative'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_accepts_fields_with_options_callback()
    {
        $definition['options_callback'] = function () { return []; };
        $this->accepts('test', $definition)->shouldReturn(true);

        $definition['options_callback'] = ['Foo', 'bar'];
        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_does_not_accept_a_field_by_default()
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    function it_formats_option_from_associative_array()
    {
        $definition = [
            'options' => ['test' => 'foo'],
        ];

        $this->format('test', 'bar', $definition)->shouldReturn('foo');
    }

    function it_formats_option_of_associative_flagged_field()
    {
        $definition = [
            'options' => array(1 => 'foo'),
            'eval'    => array('isAssociative' => true)
        ];

        $this->format(1, 'test', $definition)->shouldReturn('foo');
    }

    function it_formats_option_calling_options_callback(MockableOptionsCallback $callback)
    {
        $callback
            ->__invoke()
            ->shouldBeCalled()
            ->willReturn(['foo' => 'bar']);

        $definition = [
           'options_callback' => $callback
        ];

        $this->format('foo', 'test', $definition);
    }
}

class MockableOptionsCallback
{
    public function __invoke()
    {
        return $this->invoke();
    }

    public function invoke()
    {
    }
}
