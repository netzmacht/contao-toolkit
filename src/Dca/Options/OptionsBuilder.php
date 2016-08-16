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
     * @var Options
     */
    private $options;

    /**
     * Get Options builder for collection.
     *
     * @param Collection       $collection  Model collection.
     * @param string|\callable $labelColumn Label column or callback.
     * @param string           $valueColumn Value column.
     *
     * @return OptionsBuilder
     */
    public static function fromCollection(Collection $collection = null, $labelColumn = null, $valueColumn = 'id')
    {
        if ($collection === null) {
            return new static(new ArrayListOptions([], $valueColumn, $labelColumn));
        }

        $options = new CollectionOptions($collection, $labelColumn, $valueColumn);

        return new static($options);
    }

    /**
     * Get Options builder for collection.
     *
     * @param Result           $result      Database result.
     * @param string|\callable $labelColumn Label column or callback.
     * @param string           $valueColumn Value column.
     *
     * @return OptionsBuilder
     */
    public static function fromResult(Result $result = null, $labelColumn = null, $valueColumn = 'id')
    {
        return static::fromArrayList($result->fetchAllAssoc(), $valueColumn, $labelColumn);
    }

    /**
     * Create options from array list.
     *
     * It expects an array which is a list of associative arrays where the value column is part of the associative
     * array and has to be extracted.
     *
     * @param array            $data     Raw data list.
     * @param string|\callable $labelKey Label key or callback.
     * @param string           $valueKey Value key.
     *
     * @return OptionsBuilder
     */
    public static function fromArrayList(array $data, $labelKey = null, $valueKey = 'id')
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
            $row   = $this->options->row();
            $group = $this->groupValue($row[$column], $callback, $row);

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

            $values[$pid][$key] = array_merge($this->options->row(), ['__label__' => $value]);
        }

        $this->buildTree($values, $options, 0, $indentBy);

        $this->options = new ArrayListOptions($options, $this->options->getValueKey(), '__label__');

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
     * @param array          $row      Current data row.
     *
     * @return mixed
     */
    private function groupValue($value, $callback, array $row)
    {
        if (is_callable($callback)) {
            return $callback($value, $row);
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
