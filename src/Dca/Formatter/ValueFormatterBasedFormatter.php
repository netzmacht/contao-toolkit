<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Override;

use function is_array;

/**
 * Formatter handles the formatting of data container labels.
 */
final class ValueFormatterBasedFormatter implements Formatter
{
    /**
     * @param Definition     $definition       Data container definition.
     * @param ValueFormatter $valueFormatter   Value formatter.
     * @param ValueFormatter $optionsFormatter Options formatter.
     */
    public function __construct(
        private readonly Definition $definition,
        private readonly ValueFormatter $valueFormatter,
        private readonly ValueFormatter $optionsFormatter,
    ) {
    }

    /**
     * Format a field value.
     *
     * @param string     $field   Field name.
     * @param mixed      $value   Field value.
     * @param mixed|null $context Context object, usually the data container driver.
     *
     * @return array<int|string, string>|string|int|float|null
     */
    #[Override]
    public function formatValue(string $field, mixed $value, mixed $context = null): array|string|int|float|null
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
    #[Override]
    public function formatFieldLabel(string $field): string
    {
        return (string) $this->definition->get(['fields', $field, 'label', 0], $field);
    }

    /**
     * Format the field description.
     *
     * @param string $field Field name.
     */
    #[Override]
    public function formatFieldDescription(string $field): string
    {
        return (string) $this->definition->get(['fields', $field, 'label', 1], $field);
    }

    /**
     * Format field options.
     *
     * @param string                                            $field   Field name.
     * @param array<int|string,string|array<int|string,string>> $values  Field values.
     * @param mixed|null                                        $context Data container object.
     *
     * @return array<int|string,string|array<int|string,string>>
     */
    #[Override]
    public function formatOptions(string $field, array $values, mixed $context = null): array
    {
        $definition = $this->definition->get(['fields', $field]);

        return (array) $this->optionsFormatter->format($values, $field, $definition, $context);
    }
}
