<?php

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter\AbstractValueFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class SlugifyFilterSpec
 * @package spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter
 * @mixin SlugifyFilter
 */
class SlugifyFilterSpec extends ObjectBehavior
{
    const SEPARATOR = '-';

    const RAW_VALUE = 'Äöü -?^°#.test';

    const ALIAS_VALUE = 'aeoeue-test';

    const COLUMN = 'title';

    function let()
    {
        setlocale(LC_ALL, 'de_DE');
        $GLOBALS['TL_CONFIG']['characterSet'] = 'utf-8';
    }

    function createInstance()
    {
        $this->beConstructedWith([static::COLUMN]);
    }

    function it_is_initializable()
    {
        $this->createInstance();
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter');
    }

    function it_is_an_alias_filter()
    {
        $this->createInstance();
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Data\Alias\Filter');
    }

    function it_breaks_by_default()
    {
        $this->createInstance();
        $this->breakIfValid()->shouldReturn(true);
    }


    function it_accepts_break_option()
    {
        $this->beConstructedWith([static::COLUMN], false);
        $this->breakIfValid()->shouldReturn(false);
    }

    function it_does_not_support_repeating()
    {
        $this->createInstance();
        $this->repeatUntilValid()->shouldReturn(false);
    }

    function it_slugifies_value()
    {
        $model = (object) [static::COLUMN => static::RAW_VALUE];

        $this->createInstance();
        $this->apply($model, '', static::SEPARATOR)->shouldReturn(static::ALIAS_VALUE);
    }

    function it_supports_multiple_columns()
    {
        $aliasValue = 'aeae0032ae-12';
        $model      = (object) [
            static::COLUMN => static::RAW_VALUE,
            'test' => 'ää0032ä-´12'
        ];

        $this->beConstructedWith([static::COLUMN, 'test']);
        $this->apply($model, '', static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . $aliasValue
        );
    }

    function it_supports_custom_separator()
    {
        $aliasValue = 'aeae0032ae_12';
        $model       = (object) [
            static::COLUMN => static::RAW_VALUE,
            'test' => 'ää0032ä-´12'
        ];

        $this->beConstructedWith([static::COLUMN, 'test']);
        $this->apply($model, '', '_')->shouldReturn(
            str_replace('-', '_', static::ALIAS_VALUE) . '_' . $aliasValue
        );
    }

    function it_replaces_existing_value_by_default()
    {
        $model = (object) [
            static::COLUMN => static::RAW_VALUE,
            'id' => 5
        ];

        $this->beConstructedWith([static::COLUMN, 'id']);
        $this->apply($model, 'test', '_')->shouldReturn(
            str_replace('-', '_', static::ALIAS_VALUE) . '_' . 5
        );
    }

    function it_supports_appending()
    {
        $model = (object) [
            'id' => 5
        ];

        $this->beConstructedWith(['id'], true, AbstractValueFilter::COMBINE_APPEND);
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . 5
        );
    }

    function it_supports_prepending()
    {
        $model = (object) [
            'id' => 5
        ];

        $this->beConstructedWith(['id'], true, AbstractValueFilter::COMBINE_PREPEND);
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            5 . static::SEPARATOR . static::ALIAS_VALUE
        );
    }
}
