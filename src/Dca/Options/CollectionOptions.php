<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use Model\Collection;

/**
 * Class CollectionOptions maps a model collection to the option format.
 *
 * @package Netzmacht\Contao\DevTools\Dca\Options
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
    protected $collection;

    /**
     * The label column.
     *
     * @var string
     */
    private $labelColumn;

    /**
     * The value column.
     *
     * @var string
     */
    private $valueColumn = 'id';

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
     * @return string
     */
    public function getLabelKey()
    {
        return $this->labelColumn;
    }

    /**
     * Get the value column.
     *
     * @return string
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

    /**
     * {@inheritdoc}
     */
    public function next()
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

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->position < $this->collection->count();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
        $this->collection->reset();
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
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
    public function offsetSet($offset, $value)
    {
        // unsupported
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        // unsupported
    }

    /**
     * {@inheritdoc}
     */
    public function getArrayCopy(): array
    {
        $values = array();

        foreach ($this as $id => $value) {
            $values[$id] = $value;
        }

        return $values;
    }
}
