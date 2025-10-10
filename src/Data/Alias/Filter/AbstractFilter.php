<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;
use Override;

/**
 * Base filter class.
 */
abstract class AbstractFilter implements Filter
{
    public const COMBINE_REPLACE = 0;
    public const COMBINE_PREPEND = 1;
    public const COMBINE_APPEND  = 2;

    /**
     * @param bool $break   If true break after the filter if value is unique.
     * @param int  $combine Combine flag.
     */
    public function __construct(private readonly bool $break, private readonly int $combine = self::COMBINE_REPLACE)
    {
    }

    #[Override]
    public function breakIfValid(): bool
    {
        return $this->break;
    }

    #[Override]
    public function initialize(): void
    {
    }

    /**
     * Combine the current value with the previous one.
     *
     * @param string|null $previous  Previous alias value.
     * @param string|null $current   Current alias value.
     * @param string      $separator A separator string.
     */
    protected function combine(string|null $previous, string|null $current, string $separator): string|null
    {
        if ($previous === null || $current === null) {
            return $current;
        }

        switch ($this->combine) {
            case self::COMBINE_PREPEND:
                return $current . $separator . $previous;

            case self::COMBINE_APPEND:
                return $previous . $separator . $current;

            default:
                return $current;
        }
    }
}
