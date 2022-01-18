<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\StringUtil;

use function array_filter;
use function array_map;
use function array_values;
use function is_array;

/**
 * FileUuidFormatter converts binary file uuids to string uuids.
 */
final class FileUuidFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return ! empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'fileTree';
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        if (is_array($value)) {
            $value = array_values(
                array_filter(
                    array_map(
                        static function ($value) {
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
