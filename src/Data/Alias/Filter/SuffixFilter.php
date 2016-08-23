<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

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
     * AbstractFilter constructor.
     *
     * @param bool $break If true break after the filter if value is unique.
     * @param int  $start Start value.
     */
    public function __construct($break = true, $start = 2)
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
    }

    /**
     * {@inheritDoc}
     */
    public function repeatUntilValid()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($model, $value, $separator)
    {
        return $this->combine($value, $this->index++, $separator);
    }
}
