<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FilterBasedAliasGeneratorSpec extends ObjectBehavior
{
    public const TABLE_NAME = 'table_name';

    public const ALIAS_VALUE = 'alias';

    public function it_is_initializable(Filter $filter, Validator $validator): void
    {
        $this->beConstructedWith([$filter], $validator, self::TABLE_NAME);
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\FilterBasedAliasGenerator');
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator');
    }

    public function it_applies_filter(Filter $filter, Validator $validator): void
    {
        $this->beConstructedWith([$filter], $validator, self::TABLE_NAME);

        $model = (object) [
            'id'    => 5,
            'title' => 'Ali as',
            'alias' => 'ali-as',
        ];

        $filter->initialize()->shouldBeCalled();
        $filter->breakIfValid()->willReturn(true);
        $filter->apply($model, null, Argument::any())->willReturn(self::ALIAS_VALUE);
        $validator->validate($model, self::ALIAS_VALUE, [$model->id])->willReturn(true);

        $this->generate($model)->shouldReturn(self::ALIAS_VALUE);
    }

    public function it_applies_filters(Filter $filter, Filter $filterB, Validator $validator): void
    {
        $this->beConstructedWith([$filter, $filterB], $validator, self::TABLE_NAME);

        $model = (object) [
            'id'    => 5,
            'title' => 'Ali as',
            'alias' => 'ali-as',
        ];

        $filter->initialize()->shouldBeCalled();
        $filter->breakIfValid()->willReturn(true);
        $filter->repeatUntilValid()->willReturn(false);
        $filter->apply($model, null, Argument::any())->willReturn('foo');

        $filterB->initialize()->shouldBeCalled();
        $filterB->breakIfValid()->willReturn(true);
        $filterB->apply($model, 'foo', Argument::any())->willReturn(self::ALIAS_VALUE);

        $validator->validate($model, 'foo', [$model->id])->willReturn(false);
        $validator->validate($model, self::ALIAS_VALUE, [$model->id])->willReturn(true);

        $this->generate($model)->shouldReturn(self::ALIAS_VALUE);
    }

    public function it_throws_when_no_alias_is_generated(Validator $validator): void
    {
        $model = (object) ['id' => 5];

        $validator->validate(Argument::cetera())->willReturn(true);

        $this->beConstructedWith([], $validator, self::TABLE_NAME);
        $this
            ->shouldThrow('Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException')
            ->during('generate', [$model]);
    }

    public function it_throws_when_invalid_alias_is_generated(Validator $validator): void
    {
        $model = (object) ['id' => 5];

        $validator->validate(Argument::cetera())->willReturn(false);

        $this->beConstructedWith([], $validator, self::TABLE_NAME);
        $this
            ->shouldThrow('Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException')
            ->during('generate', [$model, self::ALIAS_VALUE]);
    }
}
