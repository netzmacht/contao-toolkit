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


class SlugifyFilter extends AbstractValueFilter
{
    /**
     * @var bool
     */
    private $preserveUppercase;

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
