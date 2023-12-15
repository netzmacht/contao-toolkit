<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use ArrayIterator;

/**
 * Class ArrayOptions decorates an already existing options array with the Options interface.
 *
 * @extends ArrayIterator<array-key,mixed>
 */
final class ArrayOptions extends ArrayIterator implements Options
{
    /** {@inheritDoc} */
    public function getArrayCopy(): array
    {
        return parent::getArrayCopy();
    }

    /** {@inheritDoc} */
    public function getLabelKey(): callable|string
    {
        return '__label__';
    }

    public function getValueKey(): string
    {
        return '__key__';
    }

    /** {@inheritDoc} */
    public function row(): array
    {
        return [
            '__key__' => $this->key(),
            '__label__' => $this->current(),
        ];
    }
}
