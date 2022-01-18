<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;

use function is_array;

/**
 * Formatter handles the formatting of data container labels.
 */
final class ValueFormatterBasedFormatter implements Formatter
{
    /**
     * Data container definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * Value formatter.
     *
     * @var ValueFormatter
     */
    private $valueFormatter;

    /**
     * Options formatter.
     *
     * @var ValueFormatter
     */
    private $optionsFormatter;

    /**
     * @param Definition     $definition       Data container definition.
     * @param ValueFormatter $valueFormatter   Value formatter.
     * @param ValueFormatter $optionsFormatter Options formatter.
     */
    public function __construct(
        Definition $definition,
        ValueFormatter $valueFormatter,
        ValueFormatter $optionsFormatter
    ) {
        $this->definition       = $definition;
        $this->valueFormatter   = $valueFormatter;
        $this->optionsFormatter = $optionsFormatter;
    }

    /**
     * Format a field value.
     *
     * @param string $field   Field name.
     * @param mixed  $value   Field value.
     * @param mixed  $context Context object, usually the data container driver.
     *
     * @return array<int|string, string>|string|null
     */
    public function formatValue(string $field, $value, $context = null)
    {
        $fieldDefinition = $this->definition->get(['fields', $field]);

        // Not found.
        if (! is_array($fieldDefinition)) {
            return '';
        }

        if ($this->valueFormatter->accepts($field, $fieldDefinition)) {
            $value = $this->valueFormatter->format($value, $field, $fieldDefinition, $context);
        }

        return $value;
    }

    /**
     * Format the field label.
     *
     * @param string $field Field name.
     */
    public function formatFieldLabel(string $field): string
    {
        return (string) $this->definition->get(['fields', $field, 'label', 0], $field);
    }

    /**
     * Format the field description.
     *
     * @param string $field Field name.
     */
    public function formatFieldDescription(string $field): string
    {
        return (string) $this->definition->get(['fields', $field, 'label', 1], $field);
    }

    /**
     * Format field options.
     *
     * @param string                                            $field   Field name.
     * @param array<int|string,string|array<int|string,string>> $values  Field values.
     * @param mixed                                             $context Data container object.
     *
     * @return array<int|string,string|array<int|string,string>>
     */
    public function formatOptions(string $field, array $values, $context = null): array
    {
        $definition = $this->definition->get(['fields', $field]);

        return (array) $this->optionsFormatter->format($values, $field, $definition, $context);
    }
}
