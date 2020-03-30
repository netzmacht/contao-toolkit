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

use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ExistingAliasFilterSpec.
 *
 * @package spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter
 * @mixin ExistingAliasFilter
 */
class ExistingAliasFilterSpec extends ObjectBehavior
{
    const SEPARATOR = '-';

    const ALIAS_VALUE = 'alias-value';

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter');
    }

    function it_is_an_alias_filter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Data\Alias\Filter');
    }

    function it_does_not_support_repeating()
    {
        $this->repeatUntilValid()->shouldReturn(false);
    }

    function it_breaks_if_unique()
    {
        $this->breakIfValid()->shouldReturn(true);
    }

    function it_returns_existing_alias(Model $model)
    {
        $this->apply($model, self::ALIAS_VALUE, self::SEPARATOR)->shouldReturn(self::ALIAS_VALUE);
    }
}
