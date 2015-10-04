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
 * ReferenceFormatter formats fields which has a reference defined.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class ReferenceFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accept($fieldName, array $fieldDefinition)
    {
        return !empty($fieldDefinition['reference']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        if (isset($fieldDefinition['reference'][$value])) {
            if (is_array($fieldDefinition['reference'][$value])) {
                list($value) = $fieldDefinition['reference'][$value];
            } else {
                $value = $fieldDefinition['reference'][$value];
            }
        }

        return $value;
    }
}
