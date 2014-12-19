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

use Model\Collection;
use Netzmacht\Contao\DevTools\Dca;

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
     * @var \Netzmacht\Contao\DevTools\Dca\Options\Options
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
    public static function fromCollection(Collection $collection, $valueColumn = 'id', $labelColumn = null)
    {
        $options = new CollectionOptions($collection);
        $options->setValueColumn($valueColumn);

        if (is_callable($labelColumn)) {
            $options->setLabelCallback($labelColumn);
        } elseif ($labelColumn) {
            $options->setLabelColumn($labelColumn);
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
     * Get the build options.
     *
     * @return Options
     */
    public function getOptions()
    {
        return $this->options;
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
}
