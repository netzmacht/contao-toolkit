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
 * RawValueFilter uses the values as given.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
final class RawValueFilter extends AbstractValueFilter
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
