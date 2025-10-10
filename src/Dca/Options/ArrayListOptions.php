<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Override;

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
     * The label key.
     *
     * @var string|callable
     */
    private $labelKey;

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
     * @param list<array<string,mixed>> $list     Array list.
     * @param callable|string|null      $labelKey Name of label key.
     * @param string                    $valueKey Name of value key.
     */
    public function __construct(
        private array $list,
        callable|string|null $labelKey = null,
        private readonly string $valueKey = 'id',
    ) {
        $this->keys     = array_keys($list);
        $this->labelKey = $labelKey ?? $valueKey;
    }

    /** {@inheritDoc} */
    #[Override]
    public function getLabelKey(): callable|string
    {
        return $this->labelKey;
    }

    /**
     * Get the value column.
     */
    #[Override]
    public function getValueKey(): string
    {
        return $this->valueKey;
    }

    /** {@inheritDoc} */
    #[Override]
    public function current(): mixed
    {
        $current = $this->list[$this->keys[$this->position]];

        if (is_callable($this->labelKey)) {
            return call_user_func($this->labelKey, $current);
        }

        return $current[$this->labelKey];
    }

    /** {@inheritDoc} */
    #[Override]
    public function row(): array
    {
        return $this->list[$this->keys[$this->position]];
    }

    #[Override]
    public function next(): void
    {
        $this->position++;
    }

    /** {@inheritDoc} */
    #[Override]
    public function key(): mixed
    {
        return $this->list[$this->keys[$this->position]][$this->valueKey];
    }

    #[Override]
    public function valid(): bool
    {
        return $this->position < count($this->keys);
    }

    #[Override]
    public function rewind(): void
    {
        $this->position = 0;
    }

    /** {@inheritDoc} */
    #[Override]
    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    /** {@inheritDoc} */
    #[Override]
    public function offsetGet($offset): array
    {
        return $this->list[$offset];
    }

    /** {@inheritDoc} */
    #[Override]
    public function offsetSet($offset, $value): void
    {
        if (! isset($this->list[$offset]) || $offset !== count($this->list)) {
            throw new InvalidArgumentException(
                'Offset ' . (string) $offset . ' has to be part of the list or a new entry',
            );
        }

        /**
         * The check above validates that offset is a valid array key
         *
         * @psalm-suppress PropertyTypeCoercion
         */
        $this->list[$offset ?? ''] = $value;
    }

    /** {@inheritDoc} */
    #[Override]
    public function offsetUnset($offset): void
    {
        unset($this->list[$offset]);
    }

    /** {@inheritDoc} */
    #[Override]
    public function getArrayCopy(): array
    {
        $values = [];

        foreach ($this as $key => $value) {
            $values[$key] = $value;
        }

        return $values;
    }
}
