<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;

/**
 * Formatter handles the formatting of data container labels.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
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
     * Formatter constructor.
     *
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
     * @return array|null|string
     */
    public function formatValue(string $field, $value, $context = null)
    {
        $fieldDefinition = $this->definition->get(['fields', $field]);

        // Not found.
        if (!is_array($fieldDefinition)) {
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
     *
     * @return string
     */
    public function formatFieldLabel(string $field): string
    {
        return (string) $this->definition->get(['fields', $field, 'label', 0], $field);
    }

    /**
     * Format the field description.
     *
     * @param string $field Field name.
     *
     * @return mixed
     */
    public function formatFieldDescription(string $field): string
    {
        return (string) $this->definition->get(['fields', $field, 'label', 1], $field);
    }

    /**
     * Format field options.
     *
     * @param string $field   Field name.
     * @param array  $values  Field values.
     * @param mixed  $context Data container object.
     *
     * @return array
     */
    public function formatOptions(string $field, array $values, $context = null): array
    {
        $definition = $this->definition->get(['fields', $field]);

        return $this->optionsFormatter->format($values, $field, $definition, $context);
    }
}
