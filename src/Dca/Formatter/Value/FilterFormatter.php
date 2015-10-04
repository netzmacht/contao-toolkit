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
 * FilterFormatter applies pre and post filters before and after formatting.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class FilterFormatter implements ValueFormatter
{
    /**
     * Pre filters are applied before formatting the value.
     *
     * @var ValueFormatter[]
     */
    private $preFilters = array();

    /**
     * Post filters are applied after formatting the value.
     *
     * @var ValueFormatter[]
     */
    private $postFilters = array();

    /**
     * Value formatter.
     *
     * @var ValueFormatter
     */
    private $formatter;

    /**
     * Construct.
     *
     * @param ValueFormatter   $formatter   The formatter.
     * @param ValueFormatter[] $preFilters  Formatters are applied as filter before formatting.
     * @param ValueFormatter[] $postFilters Formatters are applied as filter after formatting.
     */
    public function __construct(ValueFormatter $formatter, $preFilters = [], array $postFilters = [])
    {
        $this->formatter   = $formatter;
        $this->preFilters  = $preFilters;
        $this->postFilters = $postFilters;
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
        $value = $this->applyFilters($this->preFilters, $value, $fieldName, $fieldDefinition, $context);

        if ($this->formatter->accepts($fieldName, $fieldDefinition)) {
            $value = $this->formatter->format($value, $fieldName, $fieldDefinition, $context);
        }

        $value = $this->applyFilters($this->postFilters, $value, $fieldName, $fieldDefinition, $context);

        return $value;
    }

    /**
     * Apply a set of filters.
     *
     * @param ValueFormatter[] $filters         Filter formatters.
     * @param mixed            $value           Given value.
     * @param string           $fieldName       Field name.
     * @param array            $fieldDefinition Field definition.
     * @param mixed            $context         Context of the call. Usually the data container driver.
     *
     * @return mixed
     */
    private function applyFilters(array $filters, $value, $fieldName, $fieldDefinition, $context)
    {
        foreach ($filters as $filter) {
            if ($filter->accepts($fieldName, $fieldDefinition)) {
                $value = $filter->format($value, $fieldName, $fieldDefinition, $context);
            }
        }

        return $value;
    }
}
