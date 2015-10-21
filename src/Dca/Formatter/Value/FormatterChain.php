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
 * Set of multiple formatters.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class FormatterChain implements ValueFormatter
{
    /**
     * List of value formatters.
     *
     * @var ValueFormatter[]
     */
    private $formatters = array();

    /**
     * FormatterChain constructor.
     *
     * @param ValueFormatter[]|array $formatters Value formatters.
     */
    public function __construct(array $formatters)
    {
        $this->formatters = $formatters;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts($fieldName, array $fieldDefinition)
    {
        foreach ($this->formatters as $formatter) {
            if ($formatter->accepts($fieldName, $fieldDefinition)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        foreach ($this->formatters as $formatter) {
            if ($formatter->accepts($fieldName, $fieldDefinition)) {
                return $formatter->format($value, $fieldName, $fieldDefinition, $context);
            }
        }

        return $value;
    }
}
