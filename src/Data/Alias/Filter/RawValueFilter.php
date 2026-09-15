<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Override;

/**
 * RawValueFilter uses the values as given.
 *
 * @deprecated Part of the deprecated filter-based alias generator. Will be removed in 5.0.
 */
final class RawValueFilter extends AbstractValueFilter
{
    /** {@inheritDoc} */
    #[Override]
    public function apply(object $model, string|null $value, string $separator): string|null
    {
        $values = [];

        foreach ($this->columns as $column) {
            $values[] = $model->$column;
        }

        return $this->combine($value, $values, $separator);
    }
}
