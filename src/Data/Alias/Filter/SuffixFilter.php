<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * SuffixFilter adds a numeric suffix until a unique value is given.
 */
final class SuffixFilter extends AbstractFilter
{
    /**
     * The internal index counter.
     *
     * @var int
     */
    private $index = 0;

    /**
     * Start value.
     *
     * @var int
     */
    private $start;

    /**
     * Temporary value.
     *
     * @var string|null
     */
    private $value;

    /**
     * @param bool $break If true break after the filter if value is unique.
     * @param int  $start Start value.
     */
    public function __construct(bool $break = true, int $start = 2)
    {
        parent::__construct($break, self::COMBINE_APPEND);

        $this->start = $start;
    }

    public function initialize(): void
    {
        $this->index = $this->start;
        $this->value = null;
    }

    public function repeatUntilValid(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($model, $value, string $separator): string
    {
        if ($this->value === null) {
            $this->value = $value;
        }

        return $this->combine($this->value, $this->index++, $separator);
    }
}
