<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter\AbstractValueFilter;
use PhpSpec\ObjectBehavior;

use function str_replace;

class SlugifyFilterSpec extends ObjectBehavior
{
    public const SEPARATOR = '-';

    public const RAW_VALUE = 'Aou -?^#.test';

    public const ALIAS_VALUE = 'aou-test';

    public const COLUMN = 'title';

    public function let(): void
    {
        $GLOBALS['TL_CONFIG']['characterSet'] = 'utf-8';
    }

    public function createInstance(): void
    {
        $this->beConstructedWith([self::COLUMN]);
    }

    public function it_is_initializable(): void
    {
        $this->createInstance();
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter');
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

    public function it_slugifies_value(): void
    {
        $model = (object) [self::COLUMN => self::RAW_VALUE];

        $this->createInstance();
        $this->apply($model, '', self::SEPARATOR)->shouldReturn(self::ALIAS_VALUE);
    }

    public function it_supports_multiple_columns(): void
    {
        $aliasValue = 'aa0032a-12';
        $model      = (object) [
            self::COLUMN => self::RAW_VALUE,
            'test' => 'aa0032a-#12',
        ];

        $this->beConstructedWith([self::COLUMN, 'test']);
        $this->apply($model, '', self::SEPARATOR)->shouldReturn(
            self::ALIAS_VALUE . self::SEPARATOR . $aliasValue
        );
    }

    public function it_supports_custom_separator(): void
    {
        $aliasValue = 'aa0032a_12';
        $model      = (object) [
            self::COLUMN => self::RAW_VALUE,
            'test' => 'aa0032a-#12',
        ];

        $this->beConstructedWith([self::COLUMN, 'test']);
        $this->apply($model, '', '_')->shouldReturn(
            str_replace('-', '_', self::ALIAS_VALUE) . '_' . $aliasValue
        );
    }

    public function it_replaces_existing_value_by_default(): void
    {
        $model = (object) [
            self::COLUMN => self::RAW_VALUE,
            'id' => 5,
        ];

        $this->beConstructedWith([self::COLUMN, 'id']);
        $this->apply($model, 'test', '_')->shouldReturn(
            str_replace('-', '_', self::ALIAS_VALUE) . '_' . 5
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
