<?php

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;
use Netzmacht\Contao\Toolkit\Data\Alias\Generator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class GeneratorSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Data\Alias
 * @mixin Generator
 */
class GeneratorSpec extends ObjectBehavior
{
    const TABLE_NAME = 'table_name';

    const ALIAS_VALUE = 'alias';

    function it_is_initializable(Filter $filter, Validator $validator)
    {
        $this->beConstructedWith([$filter], $validator, static::TABLE_NAME);
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Generator');
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
        $this->shouldThrow('RuntimeException')->during('generate', [$model]);
    }

    function it_throws_when_invalid_alias_is_generated(Validator $validator)
    {
        $model = (object) [
            'id' => 5
        ];

        $validator->validate(Argument::cetera())->willReturn(false);

        $this->beConstructedWith([], $validator, static::TABLE_NAME);
        $this->shouldThrow('RuntimeException')->during('generate', [$model, static::ALIAS_VALUE]);
    }
}
