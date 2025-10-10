<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use Contao\Model\Collection;
use Override;

use function call_user_func;
use function is_callable;

/**
 * Class CollectionOptions maps a model collection to the option format.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class CollectionOptions implements Options
{
    /**
     * The label column.
     *
     * @var string|callable
     */
    private $labelColumn;

    /**
     * Current position.
     */
    private int $position = 0;

    /**
     * Construct.
     *
     * @param Collection           $collection  Model collection.
     * @param callable|string|null $labelColumn Name of label column.
     * @param string               $valueColumn Name of value column.
     */
    public function __construct(
        private readonly Collection $collection,
        callable|string|null $labelColumn = null,
        private readonly string $valueColumn = 'id',
    ) {
        $this->labelColumn = $labelColumn ?? $valueColumn;
    }

    /**
     * Get the label column.
     */
    #[Override]
    public function getLabelKey(): string|callable
    {
        return $this->labelColumn;
    }

    /**
     * Get the value column.
     */
    #[Override]
    public function getValueKey(): string
    {
        return $this->valueColumn;
    }

    #[Override]
    public function current(): mixed
    {
        if (is_callable($this->labelColumn)) {
            return call_user_func($this->labelColumn, $this->row());
        }

        return $this->collection->{$this->labelColumn};
    }

    /** {@inheritDoc} */
    #[Override]
    public function row(): array
    {
        return $this->collection->row();
    }

    #[Override]
    public function next(): void
    {
        $this->position++;
        $this->collection->next();
    }

    #[Override]
    public function key(): mixed
    {
        return $this->collection->{$this->valueColumn};
    }

    #[Override]
    public function valid(): bool
    {
        return $this->position < $this->collection->count();
    }

    #[Override]
    public function rewind(): void
    {
        $this->position = 0;
        $this->collection->reset();
    }

    /** {@inheritDoc} */
    #[Override]
    public function offsetExists($offset): bool
    {
        foreach ($this->collection as $row) {
            if ($row->{$this->valueColumn} === $offset) {
                return true;
            }
        }

        return false;
    }

    /** {@inheritDoc} */
    #[Override]
    public function offsetGet($offset): mixed
    {
        foreach ($this->collection as $row) {
            if ($row->{$this->valueColumn} === $offset) {
                return $row->row();
            }
        }

        return null;
    }

    /** {@inheritDoc} */
    #[Override]
    public function offsetSet($offset, $value): void
    {
        // unsupported
    }

    /** {@inheritDoc} */
    #[Override]
    public function offsetUnset($offset): void
    {
        // unsupported
    }

    /** {@inheritDoc} */
    #[Override]
    public function getArrayCopy(): array
    {
        $values = [];

        foreach ($this as $id => $value) {
            $values[$id] = $value;
        }

        return $values;
    }
}
