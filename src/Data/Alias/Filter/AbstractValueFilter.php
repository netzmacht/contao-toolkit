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
     * Model columns.
     *
     * @var list<string>
     */
    protected $columns;

    /**
     * @param list<string> $columns Columns being used for the value.
     * @param bool         $break   If true break after the filter if value is unique.
     * @param int          $combine Combine flag.
     */
    public function __construct(
        array $columns,
        bool $break = true,
        int $combine = self::COMBINE_REPLACE
    ) {
        parent::__construct($break, $combine);

        $this->columns = $columns;
    }

    public function repeatUntilValid(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function combine($previous, $current, string $separator): string
    {
        if (is_array($current)) {
            $current = implode($separator, array_filter($current));
        }

        return parent::combine($previous, $current, $separator);
    }
}
