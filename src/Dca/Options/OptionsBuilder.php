<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use Database\Result;
use Model\Collection;

/**
 * Class OptionsBuilder is designed to transfer data to the requested format for options.
 *
 * @package Netzmacht\Contao\DevTools\Dca
 */
class OptionsBuilder
{
    /**
     * The options.
     *
     * @var \Netzmacht\Contao\Toolkit\Dca\Options\Options
     */
    private $options;

    /**
     * Get Options builder for collection.
     *
     * @param Collection       $collection  Model collection.
     * @param string           $valueColumn Value column.
     * @param string|\callable $labelColumn Label column or callback.
     *
     * @return OptionsBuilder
     */
    public static function fromCollection(Collection $collection = null, $valueColumn = 'id', $labelColumn = null)
    {
        if ($collection === null) {
            return new static(new ArrayOptions());
        }

        $options = new CollectionOptions($collection);
        $options->setValueColumn($valueColumn);

        if (is_callable($labelColumn)) {
            $options->setLabelCallback($labelColumn);
        } elseif ($labelColumn) {
            $options->setLabelColumn($labelColumn);
        } else {
            $options->setLabelColumn($valueColumn);
        }

        return new static($options);
    }

    /**
     * Get Options builder for collection.
     *
     * @param Result           $result      Database result.
     * @param string           $valueColumn Value column.
     * @param string|\callable $labelColumn Label column or callback.
     *
     * @return OptionsBuilder
     */
    public static function fromResult(Result $result = null, $valueColumn = 'id', $labelColumn = null)
    {
        if ($result->numRows < 1) {
            return  new static(new ArrayOptions());
        }

        $options = new ArrayListOptions($result->fetchAllAssoc());
        $options->setValueKey($valueColumn);

        if (is_callable($labelColumn)) {
            $options->setLabelCallback($labelColumn);
        } elseif ($labelColumn) {
            $options->setLabelKey($labelColumn);
        } else {
            $options->setLabelKey($valueColumn);
        }

        return new static($options);
    }

    /**
     * Create options from array list.
     *
     * It expects an array which is a list of associatives arrays where the value column is part of the associative
     * array and has to be extracted.
     *
     * @param array            $data     Raw data list.
     * @param string           $valueKey Value key.
     * @param string|\callable $labelKey Label key or callback.
     *
     * @return OptionsBuilder
     */
    public static function fromArrayList(array $data, $valueKey = 'id', $labelKey = null)
    {
        $options = new ArrayListOptions($data, $valueKey, $labelKey);

        return new static($options);
    }

    /**
     * Construct.
     *
     * @param Options $options The options.
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * Group options by a specific column.
     *
     * @param string $column   Column name.
     * @param null   $callback Optional callback.
     *
     * @return $this
     */
    public function groupBy($column, $callback = null)
    {
        $options = array();

        foreach ($this->options as $key => $value) {
            $group = $this->groupValue($this->options[$key][$column], $callback);

            $options[$group][$key] = $value;
        }

        $this->options = new ArrayOptions($options);

        return $this;
    }

    /**
     * Get options as tree.
     *
     * @param string $parent   Column which stores parent value.
     * @param string $indentBy Indent entry by this value.
     *
     * @return $this
     */
    public function asTree($parent = 'pid', $indentBy = '-- ')
    {
        $options = array();
        $values  = array();

        foreach ($this->options as $key => $value) {
            $pid = $this->options[$key][$parent];

            $values[$pid][$key] = $value;
        }

        $this->buildTree($values, $options, 0, $indentBy);

        $this->options = new ArrayOptions($options);

        return $this;
    }

    /**
     * Get the build options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options->getArrayCopy();
    }

    /**
     * Get the group value.
     *
     * @param mixed          $value    Raw group value.
     * @param \callable|null $callback Optional callback.
     *
     * @return mixed
     */
    private function groupValue($value, $callback)
    {
        if (is_callable($callback)) {
            return $callback($value);
        }

        return $value;
    }

    /**
     * Build options tree.
     *
     * @param array  $values   The values.
     * @param array  $options  The created options.
     * @param int    $index    The current index.
     * @param string $indentBy The indent characters.
     * @param int    $depth    The current depth.
     *
     * @return mixed
     */
    private function buildTree(&$values, &$options, $index, $indentBy, $depth = 0)
    {
        if (empty($values[$index])) {
            return $options;
        }

        foreach ($values[$index] as $key => $value) {
            $options[$key] = str_repeat($indentBy, $depth) . ' ' . $value;
            $this->buildTree($values, $options, $key, $indentBy, ($depth + 1));
        }

        return $options;
    }
}
