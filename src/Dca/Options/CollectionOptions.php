<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use Contao\Model\Collection;

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
     * The database result.
     *
     * @var mixed
     */
    private $collection;

    /**
     * The label column.
     *
     * @var string|callable
     */
    private $labelColumn;

    /**
     * The value column.
     *
     * @var string
     */
    private $valueColumn;

    /**
     * Current position.
     *
     * @var int
     */
    private $position = 0;

    /**
     * Construct.
     *
     * @param Collection      $collection  Model collection.
     * @param string|callable $labelColumn Name of label column.
     * @param string          $valueColumn Name of value column.
     */
    public function __construct(Collection $collection, $labelColumn = null, string $valueColumn = 'id')
    {
        $this->collection  = $collection;
        $this->valueColumn = $valueColumn;
        $this->labelColumn = $labelColumn ?: $valueColumn;
    }

    /**
     * Get the label column.
     *
     * @return string|callable
     */
    public function getLabelKey()
    {
        return $this->labelColumn;
    }

    /**
     * Get the value column.
     */
    public function getValueKey(): string
    {
        return $this->valueColumn;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (is_callable($this->labelColumn)) {
            return call_user_func($this->labelColumn, $this->row());
        }

        return $this->collection->{$this->labelColumn};
    }

    /**
     * {@inheritdoc}
     */
    public function row(): array
    {
        return $this->collection->row();
    }

    public function next(): void
    {
        $this->position++;
        $this->collection->next();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->collection->{$this->valueColumn};
    }

    public function valid(): bool
    {
        return $this->position < $this->collection->count();
    }

    public function rewind(): void
    {
        $this->position = 0;
        $this->collection->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        foreach ($this->collection as $row) {
            if ($row->{$this->valueColumn} === $offset) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        foreach ($this->collection as $row) {
            if ($row->{$this->valueColumn} === $offset) {
                return $row->row();
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value): void
    {
        // unsupported
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        // unsupported
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayCopy(): array
    {
        $values = [];

        foreach ($this as $id => $value) {
            $values[$id] = $value;
        }

        return $values;
    }
}
