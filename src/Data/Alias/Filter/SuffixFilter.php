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

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * SuffixFilter adds a numeric suffix until a unique value is given.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
final class SuffixFilter extends AbstractFilter
{
    /**
     * The internal index counter.
     *
     * @var int
     */
    private $index;

    /**
     * Start value.
     *
     * @var int
     */
    private $start;

    /**
     * Temporary value.
     *
     * @var string
     */
    private $value;

    /**
     * AbstractFilter constructor.
     *
     * @param bool $break If true break after the filter if value is unique.
     * @param int  $start Start value.
     */
    public function __construct(bool $break = true, int $start = 2)
    {
        parent::__construct($break, static::COMBINE_APPEND);

        $this->start = (int) $start;
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        $this->index = $this->start;
        $this->value = null;
    }

    /**
     * {@inheritDoc}
     */
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
