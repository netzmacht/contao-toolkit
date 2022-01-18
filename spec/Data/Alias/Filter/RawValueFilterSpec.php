<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter\AbstractValueFilter;
use PhpSpec\ObjectBehavior;

class RawValueFilterSpec extends ObjectBehavior
{
    public const SEPARATOR = '-';

    public const ALIAS_VALUE = 'alias-value';

    public const COLUMN = 'title';

    public function createInstance(): void
    {
        $this->beConstructedWith([self::COLUMN]);
    }

    public function it_is_initializable(): void
    {
        $this->createInstance();
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\RawValueFilter');
    }

    public function it_is_an_alias_filter(): void
    {
        $this->createInstance();
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Data\Alias\Filter');
    }

    public function it_breaks_by_default(): void
    {
        $this->createInstance();
        $this->breakIfValid()->shouldReturn(true);
    }

    public function it_accepts_break_option(): void
    {
        $this->beConstructedWith([self::COLUMN], false);
        $this->breakIfValid()->shouldReturn(false);
    }

    public function it_does_not_support_repeating(): void
    {
        $this->createInstance();
        $this->repeatUntilValid()->shouldReturn(false);
    }

    public function it_uses_raw_column_value(): void
    {
        $model = (object) [self::COLUMN => self::ALIAS_VALUE];

        $this->createInstance();
        $this->apply($model, '', self::SEPARATOR)->shouldReturn(self::ALIAS_VALUE);
    }

    public function it_supports_multiple_columns(): void
    {
        $model = (object) [
            self::COLUMN => self::ALIAS_VALUE,
            'id' => 5,
        ];

        $this->beConstructedWith([self::COLUMN, 'id']);
        $this->apply($model, '', self::SEPARATOR)->shouldReturn(
            self::ALIAS_VALUE . self::SEPARATOR . 5
        );
    }

    public function it_supports_custom_separator(): void
    {
        $model = (object) [
            self::COLUMN => self::ALIAS_VALUE,
            'id' => 5,
        ];

        $this->beConstructedWith([self::COLUMN, 'id']);
        $this->apply($model, '', '_')->shouldReturn(
            self::ALIAS_VALUE . '_' . 5
        );
    }

    public function it_replaces_existing_value_by_default(): void
    {
        $model = (object) [
            self::COLUMN => self::ALIAS_VALUE,
            'id' => 5,
        ];

        $this->beConstructedWith([self::COLUMN, 'id']);
        $this->apply($model, 'test', '_')->shouldReturn(
            self::ALIAS_VALUE . '_' . 5
        );
    }

    public function it_supports_appending(): void
    {
        $model = (object) ['id' => 5];

        $this->beConstructedWith(['id'], true, AbstractValueFilter::COMBINE_APPEND);
        $this->apply($model, self::ALIAS_VALUE, self::SEPARATOR)->shouldReturn(
            self::ALIAS_VALUE . self::SEPARATOR . 5
        );
    }

    public function it_supports_prepending(): void
    {
        $model = (object) ['id' => 5];

        $this->beConstructedWith(['id'], true, AbstractValueFilter::COMBINE_PREPEND);
        $this->apply($model, self::ALIAS_VALUE, self::SEPARATOR)->shouldReturn(
            5 . self::SEPARATOR . self::ALIAS_VALUE
        );
    }
}
