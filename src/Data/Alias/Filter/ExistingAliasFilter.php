<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;
use Override;

/**
 * Class ExistingAliasFilter uses the existing value.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
final class ExistingAliasFilter implements Filter
{
    #[Override]
    public function initialize(): void
    {
    }

    #[Override]
    public function repeatUntilValid(): bool
    {
        return false;
    }

    #[Override]
    public function breakIfValid(): bool
    {
        return true;
    }

    /** {@inheritDoc} */
    #[Override]
    public function apply(object $model, string|null $value, string $separator): string|null
    {
        return $value;
    }
}
