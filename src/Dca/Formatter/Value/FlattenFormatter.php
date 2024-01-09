<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use function array_map;
use function get_object_vars;
use function implode;
use function is_array;
use function is_object;
use function is_string;

/**
 * FlattenFormatter takes an array and creates an csv value from it.
 */
final class FlattenFormatter implements ValueFormatter
{
    /** {@inheritDoc} */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return ! empty($fieldDefinition['eval']['multiple']);
    }

    /** {@inheritDoc} */
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        return $this->flatten($value);
    }

    /**
     * Flatten array or object to string values. Bypass anything else.
     *
     * This will create csv values from arrays and objects. For objects, it's public properties are used. Nested
     * arrays will be displayed with brackets.
     * $a = ['a', ['b', 'c']] will be a, [b, c].
     *
     * @param mixed $value    Current value.
     * @param bool  $brackets If true the value get brackets.
     */
    private function flatten(mixed $value, bool $brackets = false): mixed
    {
        if (is_array($value)) {
            /** @psalm-var array<int,string> $value */
            $value = array_map(
                function (mixed $value): mixed {
                    return $this->flatten($value, true);
                },
                $value,
            );

            $value = implode(', ', $value);
        } elseif (is_object($value)) {
            $value = $this->flatten(get_object_vars($value));
        } else {
            return $value;
        }

        if ($brackets && is_string($value)) {
            $value = '[' . $value . ']';
        }

        return $value;
    }
}
