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

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DeserializeFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class DeserializeFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class DeserializeFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DeserializeFormatter');
    }

    public function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_everything()
    {
        $this->accepts(uniqid(), [])->shouldReturn(true);
    }

    public function it_deserializes_arrays()
    {
        $value = ['test' => 'foo'];
        $raw   = serialize($value);

        $this->format($raw, 'test', [])->shouldReturn($value);
    }

    public function it_returns_scalar_values()
    {
        foreach ([true, false, null, 5, 5.6, 'foo'] as $value) {
            $this->format($value, 'test', [])->shouldReturn($value);
        }
    }
}
