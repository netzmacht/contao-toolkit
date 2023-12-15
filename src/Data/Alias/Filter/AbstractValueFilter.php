<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use function array_filter;
use function implode;
use function is_array;

/**
 * Base class for filters depending on values from other columns.
 */
abstract class AbstractValueFilter extends AbstractFilter
{
    /**
     * @param list<string> $columns Columns being used for the value.
     * @param bool         $break   If true break after the filter if value is unique.
     * @param int          $combine Combine flag.
     */
    public function __construct(
        protected readonly array $columns,
        bool $break = true,
        int $combine = self::COMBINE_REPLACE,
    ) {
        parent::__construct($break, $combine);
    }

    public function repeatUntilValid(): bool
    {
        return false;
    }

    /**
     * Combine the current value with the previous one.
     *
     * @param string|null              $previous  Previous alias value.
     * @param string|list<string>|null $current   Current alias value.
     * @param string                   $separator A separator string.
     */
    protected function combine(string|null $previous, string|array|null $current, string $separator): string|null
    {
        if (is_array($current)) {
            $current = implode($separator, array_filter($current));
        }

        return parent::combine($previous, $current, $separator);
    }
}
