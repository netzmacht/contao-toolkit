<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

/**
 * A ValueFormatter is responsible to format a field value.
 */
interface ValueFormatter
{
    /**
     * Check if the formatter supports the field by the passed field name.
     *
     * @param string              $fieldName       Field name.
     * @param array<string,mixed> $fieldDefinition Field definition.
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool;

    /**
     * Format a field value.
     *
     * @param mixed               $value           Given value.
     * @param string              $fieldName       Field name.
     * @param array<string,mixed> $fieldDefinition Field definition.
     * @param mixed|null          $context         Context of the call. Usually the data container driver but not
     *                                             limited to.
     */
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed;
}
