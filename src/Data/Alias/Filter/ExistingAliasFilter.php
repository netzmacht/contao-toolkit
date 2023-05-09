<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * Class ExistingAliasFilter uses the existing value.
 */
final class ExistingAliasFilter implements Filter
{
    public function initialize(): void
    {
    }

    public function repeatUntilValid(): bool
    {
        return false;
    }

    public function breakIfValid(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($model, string|null $value, string $separator): string|null
    {
        return $value;
    }
}
