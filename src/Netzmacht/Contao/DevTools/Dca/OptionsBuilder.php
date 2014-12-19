<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\DevTools\Dca;

use Model\Collection;
use Netzmacht\Contao\DevTools\Dca;
use Netzmacht\Contao\DevTools\Dca\Options\ArrayOptions;
use Netzmacht\Contao\DevTools\Dca\Options\CollectionOptions;
use Netzmacht\Contao\DevTools\Dca\Options;

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
     * Construct.
     *
     * @param Options $options The options.
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * Get Options builder for collection.
     *
     * @param Collection       $collection  Model collection.
     * @param string|\callable $labelColumn Label column or callback.
     * @param string           $valueColumn Value column.
     *
     * @return OptionsBuilder
     */
    public static function fromCollection(Collection $collection, $labelColumn, $valueColumn = 'id')
    {
        $options = new CollectionOptions($collection);
        $options->setValueColumn($valueColumn);

        if (is_callable($labelColumn)) {
            $options->setLabelCallback($labelColumn);
        } else {
            $options->setLabelColumn($labelColumn);
        }

        return new static($options);
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
            $group = $this->groupValue($this->options[$column], $callback);

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
