<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use function is_array;

/**
 * ReferenceFormatter formats fields which has a reference defined.
 */
final class ReferenceFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return ! empty($fieldDefinition['reference']);
    }

    /**
     * {@inheritDoc}
     */
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        if (isset($fieldDefinition['reference'][$value])) {
            if (is_array($fieldDefinition['reference'][$value])) {
                [$value] = $fieldDefinition['reference'][$value];
            } else {
                $value = $fieldDefinition['reference'][$value];
            }
        }

        return $value;
    }
}
