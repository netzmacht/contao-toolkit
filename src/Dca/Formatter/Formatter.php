<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

/**
 * Formatter handles the formatting of data container labels.
 */
interface Formatter
{
    /**
     * Format a field value.
     *
     * @param string     $field   Field name.
     * @param mixed      $value   Field value.
     * @param mixed|null $context Context object, usually the data container driver.
     *
     * @return array<string|int,string>|string|float|int|null
     */
    public function formatValue(string $field, mixed $value, mixed $context = null): mixed;

    /**
     * Format the field label.
     *
     * @param string $field Field name.
     */
    public function formatFieldLabel(string $field): string;

    /**
     * Format the field description.
     *
     * @param string $field Field name.
     */
    public function formatFieldDescription(string $field): string;

    /**
     * Format field options.
     *
     * @param string                                            $field   Field name.
     * @param array<int|string,array<int|string,string>|string> $values  Field values.
     * @param mixed                                             $context Data container object.
     *
     * @return array<int|string,array<int|string,string>|string>
     */
    public function formatOptions(string $field, array $values, mixed $context = null): array;
}
