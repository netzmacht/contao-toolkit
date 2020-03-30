<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use function call_user_func;
use function count;
use function is_callable;

/**
 * Class ArrayListOptions extracts options from a list of associative arrays.
 *
 * @package Netzmacht\Contao\DevTools\Dca\Options
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class ArrayListOptions implements Options
{
    /**
     * The array list.
     *
     * @var array
     */
    private $list;

    /**
     * The label key.
     *
     * @var string
     */
    private $labelKey;

    /**
     * The value key.
     *
     * @var string
     */
    private $valueKey = 'id';

    /**
     * Current position.
     *
     * @var int
     */
    private $position = 0;

    /**
     * List of keys.
     *
     * @var array
     */
    private $keys;

    /**
     * Construct.
     *
     * @param array           $list     Array list.
     * @param string|callable $labelKey Name of label key.
     * @param string          $valueKey Name of value key.
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
     *
     * @return string
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

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->position < count($this->keys);
    }

    /**
     * {@inheritdoc}
     */
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
        $values = array();

        foreach ($this as $key => $value) {
            $values[$key] = $value;
        }

        return $values;
    }
}
