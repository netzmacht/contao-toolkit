<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\Config;
use Contao\Date;
use PhpSpec\ObjectBehavior;
use ReflectionClass;

use function constant;
use function get_called_class;
use function strtoupper;
use function time;

class DateFormatterSpec extends ObjectBehavior
{
    public const DATE_FORMAT = 'd.m.Y';

    public const DATIM_FORMAT = 'd.m.Y h:i';

    public const TIME_FORMAT = 'h:i';

    public function let(): void
    {
        $reflector = new ReflectionClass(Config::class);
        $config    = $reflector->newInstanceWithoutConstructor();

        $GLOBALS['TL_CONFIG']['dateFormat']  = self::DATE_FORMAT;
        $GLOBALS['TL_CONFIG']['datimFormat'] = self::DATIM_FORMAT;
        $GLOBALS['TL_CONFIG']['timeFormat']  = self::TIME_FORMAT;

        $this->beConstructedWith($config);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DateFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_tstamp_field(): void
    {
        $this->accepts('tstamp', [])->shouldReturn(true);
    }

    public function it_accepts_date_rgxp(): void
    {
        $this->acceptsFormat('date');
    }

    public function it_accepts_datim_rgxp(): void
    {
        $this->acceptsFormat('datim');
    }

    public function it_accepts_time_rgxp(): void
    {
        $this->acceptsFormat('time');
    }

    public function it_does_not_accepts_non_date_rgxp(): void
    {
        foreach (['digit', 'email', 'uuid', 'alias'] as $format) {
            $definition['eval']['rgxp'] = $format;

            $this->accepts('date', $definition)->shouldReturn(false);
        }
    }

    public function it_formats_date_considering_config(): void
    {
        $this->formatsByConfigFormat('date');
    }

    public function it_formats_datim_considering_config(): void
    {
        $this->formatsByConfigFormat('datim');
    }

    public function it_formats_time_considering_config(): void
    {
        $this->formatsByConfigFormat('time');
    }

    private function acceptsFormat(string $format): void
    {
        $definition['eval']['rgxp'] = $format;

        $this->accepts('date', $definition)->shouldReturn(true);
    }

    private function formatsByConfigFormat(string $format): void
    {
        $const    = get_called_class() . '::' . strtoupper($format) . '_FORMAT';
        $tstamp   = time();
        $expected = Date::parse(constant($const), $tstamp);

        $definition['eval']['rgxp'] = $format;

        $this->format($tstamp, $format, $definition)->shouldReturn($expected);
    }
}
