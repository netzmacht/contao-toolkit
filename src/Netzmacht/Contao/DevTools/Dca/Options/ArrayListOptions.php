<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\DevTools\Dca\Options;

/**
 * Class ArrayListOptions extracts options from a list of associative arrays.
 *
 * @package Netzmacht\Contao\DevTools\Dca\Options
 */
class ArrayListOptions implements Options
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
     * Instead of a label key you can define a callable.
     *
     * @var \callable
     */
    private $labelCallback;

    /**
     * Current position.
     *
     * @var int
     */
    private $position = 0;

    /**
     * Construct.
     *
     * @param array  $list     Array list.
     * @param string $valueKey Name of value key.
     * @param string $labelKey Name of label key.
     */
    public function __construct(array $list, $valueKey = 'id', $labelKey = null)
    {
        $this->list     = $list;
        $this->keys     = array_keys($list);
        $this->labelKey = $labelKey;
        $this->valueKey = $valueKey;
    }

    /**
     * Get the label column.
     *
     * @return string
     */
    public function getLabelKey()
    {
        return $this->labelKey;
    }

    /**
     * Set label column.
     *
     * @param string $labelKey Label column.
     *
     * @return $this
     */
    public function setLabelKey($labelKey)
    {
        $this->labelKey = $labelKey;

        return $this;
    }

    /**
     * Get the value column.
     *
     * @return string
     */
    public function getValueKey()
    {
        return $this->valueKey;
    }

    /**
     * Set the value column.
     *
     * @param string $valueKey Value column.
     *
     * @return $this
     */
    public function setValueKey($valueKey)
    {
        $this->valueKey = $valueKey;

        return $this;
    }

    /**
     * Set the label callback.
     *
     * If a label callback is defined it is used no matter if any label column is selection. The callback gets the
     * current Model as argument.
     *
     * @param callable $labelCallback Label callback.
     *
     * @return $this
     */
    public function setLabelCallback($labelCallback)
    {
        $this->labelCallback = $labelCallback;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $current = $this->list[$this->keys[$this->position]];

        if ($this->labelCallback) {
            $callback = $this->labelCallback;

            return $callback($current);
        }

        return $current[$this->labelKey];
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->keys[$this->position];
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->position < count($this->keys);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
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
    public function offsetSet($offset, $value)
    {
        $this->list[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->list[$offset]);
    }
}
