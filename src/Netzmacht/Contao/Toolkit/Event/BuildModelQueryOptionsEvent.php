<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * BuildModelQueryOptionsEvent is designed to build dynamically the queries for Contao models.
 *
 * It can be used inside of closed components like content elements, modules etc. to allow 3rd party developers to
 * provide extra queries conditions.
 *
 */
class BuildModelQueryOptionsEvent extends Event
{
    const NAME = 'toolkit.model.build-query-options';

    /**
     * The query column conditions.
     *
     * @var \ArrayObject
     */
    private $column;

    /**
     * The query conditions values.
     *
     * @var \ArrayObject
     */
    private $value;

    /**
     * The query options.
     *
     * @var \ArrayObject
     */
    private $options;

    /**
     * The context in which the query is created.
     *
     * @var object|null
     */
    private $context;

    /**
     * The table name.
     *
     * @var string
     */
    private $table;

    /**
     * BuildQueryOptionsEvent constructor.
     *
     * @param string $table   The table name.
     * @param object $context The context object where the query is built.
     * @param array  $column  The query column conditions.
     * @param array  $value   The query conditions values.
     * @param array  $options The query options.
     */
    public function __construct(
        $table,
        $context = null,
        array $column = array(),
        array $value = array(),
        array $options = array()
    ) {
        $this->table   = $table;
        $this->context = $context;
        $this->column  = new \ArrayObject($column);
        $this->value   = new \ArrayObject($value);
        $this->options = new \ArrayObject($options);
    }

    /**
     * Get column.
     *
     * @param bool $arrayCopy If true an array copy is returned.
     *
     * @return \ArrayObject
     */
    public function getColumn($arrayCopy = false)
    {
        return $arrayCopy ? $this->column->getArrayCopy() : $this->column;
    }

    /**
     * Get value.
     *
     * @param bool $arrayCopy If true an array copy is returned.
     *
     * @return \ArrayObject
     */
    public function getValue($arrayCopy = false)
    {
        return $arrayCopy ? $this->value->getArrayCopy() : $this->value;
    }

    /**
     * Get options.
     *
     * @param bool $arrayCopy If true an array copy is returned.
     *
     * @return \ArrayObject
     */
    public function getOptions($arrayCopy = false)
    {
        return $arrayCopy ? $this->options->getArrayCopy() : $this->options;
    }

    /**
     * Get context.
     *
     * @return object|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get table.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }
}
