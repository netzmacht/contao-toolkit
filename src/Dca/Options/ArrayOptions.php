<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use ArrayIterator;

/**
 * Class ArrayOptions decorates an already existing options array with the Options interface.
 */
final class ArrayOptions extends ArrayIterator implements Options
{
    /**
     * {@inheritdoc}
     */
    public function getArrayCopy(): array
    {
        return parent::getArrayCopy();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelKey()
    {
        return '__label__';
    }

    public function getValueKey(): string
    {
        return '__key__';
    }

    /**
     * {@inheritdoc}
     */
    public function row(): array
    {
        return [
            '__key__' => $this->key(),
            '__label__' => $this->current(),
        ];
    }
}
