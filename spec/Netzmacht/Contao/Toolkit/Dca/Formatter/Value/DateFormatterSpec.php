<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\Config;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DateFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class DateFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin DateFormatter
 */
class DateFormatterSpec extends ObjectBehavior
{
    const DATE_FORMAT = 'd.m.Y';

    const DATIM_FORMAT = 'd.m.Y h:i';

    const TIME_FORMAT = 'h:i';

    function let()
    {
        $reflector = new \ReflectionClass('Contao\Config');
        $config    = $reflector->newInstanceWithoutConstructor();

        $GLOBALS['TL_CONFIG']['dateFormat']  = static::DATE_FORMAT;
        $GLOBALS['TL_CONFIG']['datimFormat'] = static::DATIM_FORMAT;
        $GLOBALS['TL_CONFIG']['timeFormat']  = static::TIME_FORMAT;

        $this->beConstructedWith($config);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DateFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_tstamp_field()
    {
        $this->accepts('tstamp', [])->shouldReturn(true);
    }

    function it_accepts_date_rgxp()
    {
        $this->acceptsFormat('date');
    }

    function it_accepts_datim_rgxp()
    {
        $this->acceptsFormat('datim');
    }

    function it_accepts_time_rgxp()
    {
        $this->acceptsFormat('time');
    }

    function it_does_not_accepts_non_date_rgxp()
    {
        foreach (['digit', 'email', 'uuid', 'alias'] as $format) {
            $definition['eval']['rgxp'] = $format;

            $this->accepts('date', $definition)->shouldReturn(false);
        }
    }

    function it_formats_date_considering_config()
    {
        $this->formatsByConfigFormat('date');
    }

    function it_formats_datim_considering_config()
    {
        $this->formatsByConfigFormat('datim');
    }

    function it_formats_time_considering_config()
    {
        $this->formatsByConfigFormat('time');
    }

    private function acceptsFormat($format)
    {
        $definition['eval']['rgxp'] = $format;

        $this->accepts('date', $definition)->shouldReturn(true);
    }

    private function formatsByConfigFormat($format)
    {
        $const    = get_called_class() . '::' .  strtoupper($format) . '_FORMAT';
        $tstamp   = time();
        $expected = \Date::parse(constant($const), $tstamp);

        $definition['eval']['rgxp'] = $format;

        $this->format($tstamp, $format, $definition)->shouldReturn($expected);
    }
}
