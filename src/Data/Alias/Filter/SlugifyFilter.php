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

/**
 * SlugifyFilter creates a slug value of the columns being representated.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
class SlugifyFilter extends AbstractValueFilter
{
    /**
     * Preserve uppercase.
     *
     * @var bool
     */
    private $preserveUppercase;

    /**
     * Construct.
     *
     * @param array $columns           Columns being used for the value.
     * @param bool  $break             If true break after the filter if value is unique.
     * @param int   $combine           Combine flag.
     * @param bool  $preserveUppercase If true uppercase values are not transformed.
     */
    public function __construct(
        array $columns,
        $break = true,
        $combine = self::COMBINE_REPLACE,
        $preserveUppercase = false
    ) {
        parent::__construct($columns, $break, $combine);

        $this->preserveUppercase = (bool) $preserveUppercase;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($model, $value, $separator)
    {
        $values = array();

        foreach ($this->columns as $column) {
            $values[] = standardize($model->$column, $this->preserveUppercase);
        }

        return $this->combine($value, $values, $separator);
    }
}
