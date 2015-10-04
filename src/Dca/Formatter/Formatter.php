<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;

/**
 * Formatter handles the formatting of data container labels.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
class Formatter
{
    /**
     * @var Definition
     */
    private $definition;

    /**
     *
     * @var ValueFormatter
     */
    private $valueFormatter;

    /**
     * Formatter constructor.
     *
     * @param Definition     $definition
     * @param ValueFormatter $valueFormatter
     */
    public function __construct(Definition $definition, ValueFormatter $valueFormatter)
    {
        $this->definition     = $definition;
        $this->valueFormatter = $valueFormatter;
    }

    /**
     * Format a field value.
     *
     * @param string $field Field name.
     * @param mixed  $value Field value.
     *
     * @return array|null|string
     */
    public function formatValue($field, $value)
    {
        $fieldDefinition = $this->definition->get(['fields', $field]);

        // Not found.
        if (!is_array($fieldDefinition)) {
            return '';
        }

        if ($this->valueFormatter->accepts($field, $fieldDefinition)) {
            $value = $this->valueFormatter->format($value, $field, $fieldDefinition);
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
    public function formatFieldLabel($field)
    {
        return $this->definition->get(['fields', $field, 'label', 0], $field);
    }

    /**
     * Format the field description.
     *
     * @param string $field Field name.
     *
     * @return mixed
     */
    public function formatFieldDescription($field)
    {
        return $this->definition->get(['fields', $field, 'label', 1], $field);
    }

    /**
     * Format field options.
     *
     * @param string $field  Field name.
     * @param array  $values Field values.
     *
     * @return array
     */
    public function formatOptions($field, array $values)
    {
        $labels = $this->definition->get(['fields', $field, 'reference']);
        if (!$labels) {
            return $values;
        }

        $options = array();

        foreach ($values as $key => $value) {
            if (array_key_exists($key, $labels)) {
                $options[$key] = $labels[$key];
            }
        }

        return $options;
    }
}
