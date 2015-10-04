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


class RawValueFilter extends AbstractValueFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($model, $value, $separator)
    {
        $values = array();

        foreach ($this->columns as $column) {
            $values[] = $model->$column;
        }

        return $this->combine($value, $values, $separator);
    }
}
