<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Contao\Model;
use PhpSpec\ObjectBehavior;

class ExistingAliasFilterSpec extends ObjectBehavior
{
    public const SEPARATOR = '-';

    public const ALIAS_VALUE = 'alias-value';

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter');
    }

    public function it_is_an_alias_filter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Data\Alias\Filter');
    }

    public function it_does_not_support_repeating(): void
    {
        $this->repeatUntilValid()->shouldReturn(false);
    }

    public function it_breaks_if_unique(): void
    {
        $this->breakIfValid()->shouldReturn(true);
    }

    public function it_returns_existing_alias(Model $model): void
    {
        $this->apply($model, self::ALIAS_VALUE, self::SEPARATOR)->shouldReturn(self::ALIAS_VALUE);
    }
}
