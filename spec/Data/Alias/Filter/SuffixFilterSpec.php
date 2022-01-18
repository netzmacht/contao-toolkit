<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use PhpSpec\ObjectBehavior;

class SuffixFilterSpec extends ObjectBehavior
{
    public const SEPARATOR = '-';

    public const ALIAS_VALUE = 'alias-value';

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter');
    }

    public function it_is_an_alias_filter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Data\Alias\Filter');
    }

    public function it_breaks_by_default(): void
    {
        $this->breakIfValid()->shouldReturn(true);
    }

    public function it_accepts_break_option(): void
    {
        $this->beConstructedWith(false);
        $this->breakIfValid()->shouldReturn(false);
    }

    public function it_supports_repeating(): void
    {
        $this->repeatUntilValid()->shouldReturn(true);
    }

    public function it_appends_suffix(): void
    {
        $model = (object) [];

        $this->initialize();
        $this->apply($model, self::ALIAS_VALUE, self::SEPARATOR)->shouldReturn(
            self::ALIAS_VALUE . self::SEPARATOR . 2
        );
    }

    public function it_accepts_custom_separator(): void
    {
        $model = (object) [];

        $this->initialize();
        $this->apply($model, self::ALIAS_VALUE, '_')->shouldReturn(
            self::ALIAS_VALUE . '_' . 2
        );
    }

    public function it_increases_suffix_when_repeating(): void
    {
        $model = (object) [];

        $this->initialize();
        $this->apply($model, self::ALIAS_VALUE, self::SEPARATOR)->shouldReturn(
            self::ALIAS_VALUE . self::SEPARATOR . 2
        );
        $this->apply($model, self::ALIAS_VALUE, self::SEPARATOR)->shouldReturn(
            self::ALIAS_VALUE . self::SEPARATOR . 3
        );
        $this->apply($model, self::ALIAS_VALUE, self::SEPARATOR)->shouldReturn(
            self::ALIAS_VALUE . self::SEPARATOR . 4
        );
    }

    public function it_accepts_custom_start_value(): void
    {
        $model = (object) [];

        $this->beConstructedWith(true, 5);
        $this->initialize();
        $this->apply($model, self::ALIAS_VALUE, '_')->shouldReturn(
            self::ALIAS_VALUE . '_' . 5
        );
    }
}
