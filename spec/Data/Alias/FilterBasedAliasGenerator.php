<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;
use Netzmacht\Contao\Toolkit\Data\Alias\FilterBasedAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class GeneratorSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Data\Alias
 * @mixin FilterBasedAliasGenerator
 */
class FilterBasedAliasGeneratorSpec extends ObjectBehavior
{
    const TABLE_NAME = 'table_name';

    const ALIAS_VALUE = 'alias';

    function it_is_initializable(Filter $filter, Validator $validator)
    {
        $this->beConstructedWith([$filter], $validator, static::TABLE_NAME);
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\FilterBasedAliasGenerator');
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator');
    }

    function it_applies_filter(Filter $filter, Validator $validator)
    {
        $this->beConstructedWith([$filter], $validator, static::TABLE_NAME);

        $model = (object) [
            'id'    => 5,
            'title' => 'Ali as',
            'alias' => 'ali-as'
        ];

        $filter->initialize()->shouldBeCalled();
        $filter->breakIfValid()->willReturn(true);
        $filter->apply($model, null, Argument::any())->willReturn(static::ALIAS_VALUE);
        $validator->validate(static::ALIAS_VALUE, [$model->id])->willReturn(true);

        $this->generate($model)->shouldReturn(static::ALIAS_VALUE);
    }

    function it_applies_filters(Filter $filter, Filter $filterB, Validator $validator)
    {
        $this->beConstructedWith([$filter, $filterB], $validator, static::TABLE_NAME);

        $model = (object) [
            'id'    => 5,
            'title' => 'Ali as',
            'alias' => 'ali-as'
        ];

        $filter->initialize()->shouldBeCalled();
        $filter->breakIfValid()->willReturn(true);
        $filter->repeatUntilValid()->willReturn(false);
        $filter->apply($model, null, Argument::any())->willReturn('foo');

        $filterB->initialize()->shouldBeCalled();
        $filterB->breakIfValid()->willReturn(true);
        $filterB->apply($model, 'foo', Argument::any())->willReturn(static::ALIAS_VALUE);

        $validator->validate('foo', [$model->id])->willReturn(false);
        $validator->validate(static::ALIAS_VALUE, [$model->id])->willReturn(true);

        $this->generate($model)->shouldReturn(static::ALIAS_VALUE);
    }

    function it_throws_when_no_alias_is_generated(Validator $validator)
    {
        $model = (object) [
            'id' => 5
        ];

        $validator->validate(Argument::cetera())->willReturn(true);

        $this->beConstructedWith([], $validator, static::TABLE_NAME);
        $this->shouldThrow('Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException')->during('generate', [$model]);
    }

    function it_throws_when_invalid_alias_is_generated(Validator $validator)
    {
        $model = (object) [
            'id' => 5
        ];

        $validator->validate(Argument::cetera())->willReturn(false);

        $this->beConstructedWith([], $validator, static::TABLE_NAME);
        $this->shouldThrow('Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException')->during('generate', [$model, static::ALIAS_VALUE]);
    }
}
