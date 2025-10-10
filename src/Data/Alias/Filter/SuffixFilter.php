<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Override;

/**
 * SuffixFilter adds a numeric suffix until a unique value is given.
 */
final class SuffixFilter extends AbstractFilter
{
    /**
     * The internal index counter.
     */
    private int $index = 0;

    /**
     * Temporary value.
     */
    private string|null $value = null;

    /**
     * @param bool $break If true break after the filter if value is unique.
     * @param int  $start Start value.
     */
    public function __construct(bool $break = true, private readonly int $start = 2)
    {
        parent::__construct($break, self::COMBINE_APPEND);
    }

    #[Override]
    public function initialize(): void
    {
        $this->index = $this->start;
        $this->value = null;
    }

    #[Override]
    public function repeatUntilValid(): bool
    {
        return true;
    }

    /** {@inheritDoc} */
    #[Override]
    public function apply(object $model, string|null $value, string $separator): string|null
    {
        if ($this->value === null) {
            $this->value = $value;
        }

        return $this->combine($this->value, (string) $this->index++, $separator);
    }
}
