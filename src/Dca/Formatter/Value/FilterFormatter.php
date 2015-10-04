<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

/**
 * FilterFormatter applies formatters used as filters to an value.
 *
 * The difference between the FormatterChain and the FilterFormatter is that the filter formatter will apply all
 * rules and modifies the given value.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class FilterFormatter implements ValueFormatter
{
    /**
     * List of filters.
     *
     * @var ValueFormatter[]
     */
    private $filters = array();

    /**
     * Construct.
     *
     * @param ValueFormatter[]|array $filters List of filters.
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts($fieldName, array $fieldDefinition)
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        foreach ($this->filters as $filter) {
            if ($filter->accepts($fieldName, $fieldDefinition)) {
                $value = $filter->format($value, $fieldName, $fieldDefinition, $context);
            }
        }

        return $value;
    }
}
