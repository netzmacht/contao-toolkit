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
 * FlattenFormatter takes an array and creates an csv value from it.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class FlattenFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts($fieldName, array $fieldDefinition)
    {
        return !empty($fieldDefinition['eval']['multiple']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        if (is_array($value)) {
            return implode(', ', $value);
        }

        return $value;
    }
}
