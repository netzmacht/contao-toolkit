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
 * FileUuidFormatter converts binary file uuids to string uuids.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class FileUuidFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accept($fieldName, array $fieldDefinition)
    {
        return (!empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'fileTree');
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        if (is_array($value)) {
            $value = array_map(
                function ($value) {
                    return $value ? \String::binToUuid($value) : '';
                },
                $value
            );
        } else {
            $value = $value ? \String::binToUuid($value) : '';
        }

        return $value;
    }
}
