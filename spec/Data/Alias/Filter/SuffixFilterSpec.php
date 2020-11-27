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

use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class SuffixFilterSpec
 */
class SuffixFilterSpec extends ObjectBehavior
{
    const SEPARATOR = '-';

    const ALIAS_VALUE = 'alias-value';

    public function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter');
    }

    public function it_is_an_alias_filter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Data\Alias\Filter');
    }

    public function it_breaks_by_default()
    {
        $this->breakIfValid()->shouldReturn(true);
    }

    public function it_accepts_break_option()
    {
        $this->beConstructedWith(false);
        $this->breakIfValid()->shouldReturn(false);
    }

    public function it_supports_repeating()
    {
        $this->repeatUntilValid()->shouldReturn(true);
    }

    public function it_appends_suffix()
    {
        $model = (object) [];

        $this->initialize();
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . 2
        );
    }

    public function it_accepts_custom_separator()
    {
        $model = (object) [];

        $this->initialize();
        $this->apply($model, static::ALIAS_VALUE, '_')->shouldReturn(
            static::ALIAS_VALUE . '_' . 2
        );
    }

    public function it_increases_suffix_when_repeating()
    {
        $model = (object) [];

        $this->initialize();
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . 2
        );
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . 3
        );
        $this->apply($model, static::ALIAS_VALUE, static::SEPARATOR)->shouldReturn(
            static::ALIAS_VALUE . static::SEPARATOR . 4
        );
    }

    public function it_accepts_custom_start_value()
    {
        $model = (object) [];

        $this->beConstructedWith(true, 5);
        $this->initialize();
        $this->apply($model, static::ALIAS_VALUE, '_')->shouldReturn(
            static::ALIAS_VALUE . '_' . 5
        );
    }
}
