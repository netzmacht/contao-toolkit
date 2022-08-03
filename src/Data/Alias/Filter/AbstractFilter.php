<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * Base filter class.
 */
abstract class AbstractFilter implements Filter
{
    public const COMBINE_REPLACE = 0;
    public const COMBINE_PREPEND = 1;
    public const COMBINE_APPEND  = 2;

    /**
     * If true break after the filter if value is unique.
     */
    private bool $break;

    /**
     * Combine flag.
     */
    private int $combine;

    /**
     * @param bool $break   If true break after the filter if value is unique.
     * @param int  $combine Combine flag.
     */
    public function __construct(bool $break, int $combine = self::COMBINE_REPLACE)
    {
        $this->break   = $break;
        $this->combine = $combine;
    }

    public function breakIfValid(): bool
    {
        return $this->break;
    }

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
    protected function combine(?string $previous, ?string $current, string $separator): ?string
    {
        if (! $previous || ! $current) {
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
