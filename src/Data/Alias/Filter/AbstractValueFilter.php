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
 * Base class for filters depending on values from other columns.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
abstract class AbstractValueFilter extends AbstractFilter
{
    /**
     * Model columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * SlugifyFilter constructor.
     *
     * @param array $columns Columns being used for the value.
     * @param bool  $break   If true break after the filter if value is unique.
     * @param int   $combine Combine flag.
     */
    public function __construct(
        array $columns,
        $break = true,
        $combine = self::COMBINE_REPLACE
    ) {
        parent::__construct($break, $combine);

        $this->columns = $columns;
    }

    /**
     * {@inheritDoc}
     */
    public function repeatUntilValid()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function combine($previous, $current, $separator)
    {
        if (is_array($current)) {
            $current = implode($separator, array_filter($current));
        }

        return parent::combine($previous, $current, $separator);
    }
}
