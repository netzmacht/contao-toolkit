<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\StringUtil;
use Override;

/**
 * DeserializeFormatter deserialize any value.
 */
final class DeserializeFormatter implements ValueFormatter
{
    /** {@inheritDoc} */
    #[Override]
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return true;
    }

    /** {@inheritDoc} */
    #[Override]
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        return StringUtil::deserialize($value);
    }
}
