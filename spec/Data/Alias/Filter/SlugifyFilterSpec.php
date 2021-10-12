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
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class SlugifyFilterSpec
 */
class SlugifyFilterSpec extends ObjectBehavior
{
    const SEPARATOR = '-';

    const RAW_VALUE = 'Aou -?^#.test';

    const ALIAS_VALUE = 'aou-test';

    const COLUMN = 'title';

    public function let()
    {
        $GLOBALS['TL_CONFIG']['characterSet'] = 'utf-8';
    }

    public function createInstance()
    {
        $this->beConstructedWith([static::COLUMN]);
    }

    public function it_is_initializable()
    {
        $this->createInstance();
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter');
    }

    public function it_is_an_alias_filter()
    {
        $this->createInstance();
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Data\Alias\Filter');
    }

    public function it_breaks_by_default()
    {
        $this->createInstance();
        $this->breakIfValid()->shouldReturn(true);
    }


    public function it_accepts_break_option()
    {
        $this->beConstructedWith([static::COLUMN], false);
        $this->breakIfValid()->shouldReturn(false);
    }

    public function it_does_not_support_repeating()
    {
        $this->createInstance();
        $this->repeatUntilValid()->shouldReturn(false);
    }

    public function it_slugifies_value()
    {
        $model = (object) [static::COLUMN => static::RAW_VALUE];

        $this->createInstance();
        $this->apply($model, '', static::SEPARATOR)->shouldReturn(static::ALIAS_VALUE);
    }

    public function it_supports_multiple_columns()
    {
        $aliasValue = 'aa0032a-12';
        $model      = (object) [
            static::COLUMN => static::RAW_VALUE,
            'test' => 'aa0032a-#12'
        ];

        $this->beConstructedWith([static::COLUMN, 'test']);
        $this->apply($model, '', static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . $aliasValue
        );
    }

    public function it_supports_custom_separator()
    {
        $aliasValue = 'aa0032a_12';
        $model      = (object) [
            static::COLUMN => static::RAW_VALUE,
            'test' => 'aa0032a-#12'
        ];

        $this->beConstructedWith([static::COLUMN, 'test']);
        $this->apply($model, '', '_')->shouldReturn(
            str_replace('-', '_', static::ALIAS_VALUE) . '_' . $aliasValue
        );
    }

    public function it_replaces_existing_value_by_default()
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

    public function it_supports_appending()
    {
        $model = (object) [
            'id' => 5
        ];

        $this->beConstructedWith(['id'], true, AbstractValueFilter::COMBINE_APPEND);
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . 5
        );
    }

    public function it_supports_prepending()
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
