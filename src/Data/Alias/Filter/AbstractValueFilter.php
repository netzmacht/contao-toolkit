<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;


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
     * @param array $columns
     * @param bool  $break
     * @param int   $combine
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
     * @inheritDoc
     */
    public function repeatUntilUnique()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function combine($previous, $current, $separator)
    {
        if (is_array($current)) {
            $current = implode($separator, array_filter($current));
        }

        return parent::combine($previous, $current, $separator);
    }
}
