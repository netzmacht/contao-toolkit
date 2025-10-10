<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\StringUtil;
use Override;

use function array_filter;
use function array_map;
use function array_values;
use function is_array;

/**
 * FileUuidFormatter converts binary file uuids to string uuids.
 */
final class FileUuidFormatter implements ValueFormatter
{
    /** {@inheritDoc} */
    #[Override]
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return ! empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'fileTree';
    }

    /** {@inheritDoc} */
    #[Override]
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        if (is_array($value)) {
            $value = array_values(
                array_filter(
                    array_map(
                        static function ($value) {
                            return $value ? StringUtil::binToUuid($value) : '';
                        },
                        $value,
                    ),
                ),
            );
        } else {
            $value = $value ? StringUtil::binToUuid($value) : '';
        }

        return $value;
    }
}
