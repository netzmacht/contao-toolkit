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

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter\AbstractValueFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\RawValueFilter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class RawValueFilterSpec
 * @package spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter
 * @mixin RawValueFilter
 */
class RawValueFilterSpec extends ObjectBehavior
{
    const SEPARATOR = '-';

    const ALIAS_VALUE = 'alias-value';

    const COLUMN = 'title';

    function createInstance()
    {
        $this->beConstructedWith([static::COLUMN]);
    }

    function it_is_initializable()
    {
        $this->createInstance();
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\RawValueFilter');
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

    function it_uses_raw_column_value()
    {
        $model = (object) [static::COLUMN => static::ALIAS_VALUE];

        $this->createInstance();
        $this->apply($model, '', static::SEPARATOR)->shouldReturn(static::ALIAS_VALUE);
    }

    function it_supports_multiple_columns()
    {
        $model = (object) [
            static::COLUMN => static::ALIAS_VALUE,
            'id' => 5
        ];

        $this->beConstructedWith([static::COLUMN, 'id']);
        $this->apply($model, '', static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . 5
        );
    }

    function it_supports_custom_separator()
    {
        $model = (object) [
            static::COLUMN => static::ALIAS_VALUE,
            'id' => 5
        ];

        $this->beConstructedWith([static::COLUMN, 'id']);
        $this->apply($model, '', '_')->shouldReturn(
            static::ALIAS_VALUE . '_' . 5
        );
    }

    function it_replaces_existing_value_by_default()
    {
        $model = (object) [
            static::COLUMN => static::ALIAS_VALUE,
            'id' => 5
        ];

        $this->beConstructedWith([static::COLUMN, 'id']);
        $this->apply($model, 'test', '_')->shouldReturn(
            static::ALIAS_VALUE . '_' . 5
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
