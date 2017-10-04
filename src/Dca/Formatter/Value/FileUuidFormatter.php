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

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\StringUtil;

/**
 * FileUuidFormatter converts binary file uuids to string uuids.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class FileUuidFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts($fieldName, array $fieldDefinition)
    {
        return (!empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'fileTree');
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        if (is_array($value)) {
            $value = array_values(
                array_filter(
                    array_map(
                        function ($value) {
                            return $value ? StringUtil::binToUuid($value) : '';
                        },
                        $value
                    )
                )
            );
        } else {
            $value = $value ? StringUtil::binToUuid($value) : '';
        }

        return $value;
    }
}
