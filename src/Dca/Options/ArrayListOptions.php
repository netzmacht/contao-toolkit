<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use function array_keys;
use function call_user_func;
use function count;
use function is_callable;

/**
 * Class ArrayListOptions extracts options from a list of associative arrays.
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class ArrayListOptions implements Options
{
    /**
     * The array list.
     *
     * @var list<array<string,mixed>>
     */
    private array $list;

    /**
     * The label key.
     *
     * @var string|callable
     */
    private $labelKey;

    /**
     * The value key.
     */
    private string $valueKey = 'id';

    /**
     * Current position.
     */
    private int $position = 0;

    /**
     * List of keys.
     *
     * @var list<int>
     */
    private array $keys;

    /**
     * Construct.
     *
     * @param list<array<string,mixed>> $list     Array list.
     * @param string|callable           $labelKey Name of label key.
     * @param string                    $valueKey Name of value key.
     */
    public function __construct(array $list, $labelKey = null, string $valueKey = 'id')
    {
        $this->list     = $list;
        $this->keys     = array_keys($list);
        $this->labelKey = $labelKey ?: $valueKey;
        $this->valueKey = $valueKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelKey()
    {
        return $this->labelKey;
    }

    /**
     * Get the value column.
     */
    public function getValueKey(): string
    {
        return $this->valueKey;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $current = $this->list[$this->keys[$this->position]];

        if (is_callable($this->labelKey)) {
            return call_user_func($this->labelKey, $current);
        }

        return $current[$this->labelKey];
    }

    /**
     * {@inheritdoc}
     */
    public function row(): array
    {
        return $this->list[$this->keys[$this->position]];
    }

    public function next(): void
    {
        $this->position++;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->list[$this->keys[$this->position]][$this->valueKey];
    }

    public function valid(): bool
    {
        return $this->position < count($this->keys);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->list[$offset];
    }

    /**
     * {@inheritdoc}
     *
     * @psalm-suppress PossiblyNullArrayOffset
     */
    public function offsetSet($offset, $value): void
    {
        $this->list[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->list[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayCopy(): array
    {
        $values = [];

        foreach ($this as $key => $value) {
            $values[$key] = $value;
        }

        return $values;
    }
}
