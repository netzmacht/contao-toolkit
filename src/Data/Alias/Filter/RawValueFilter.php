<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * RawValueFilter uses the values as given.
 */
final class RawValueFilter extends AbstractValueFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($model, $value, string $separator): string
    {
        $values = [];

        foreach ($this->columns as $column) {
            $values[] = $model->$column;
        }

        return $this->combine($value, $values, $separator);
    }
}
