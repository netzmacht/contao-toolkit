<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca;

/**
 * Base class for data container callback classes.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
abstract class Callbacks
{
    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name;

    /**
     * Get data container name.
     *
     * @return string
     */
    public static function getName()
    {
        return static::$name;
    }

    /**
     * Generate the callback definition.
     *
     * This method is used as PHP 5.4 is supported right now. Otherwise the recommend callback notation would rather be
     * [Callbacks::class, 'methodName'].
     *
     * @param string $name Callback method.
     *
     * @return array
     */
    public static function callback($name)
    {
        return [get_called_class(), $name];
    }

    /**
     * Get a definition.
     *
     * @param string|null $name Data definition name. If empty the default name is used.
     *
     * @return Definition
     */
    protected static function getDefinition($name = null)
    {
        return static::getServiceContainer()->getDcaManager()->getDefinition($name ?: static::getName());
    }

    /**
     * Get a formatter.
     *
     * @param string|null $name Data definition name. If empty the default name is used.
     *
     * @return Formatter\Formatter
     */
    protected static function getFormatter($name = null)
    {
        return static::getServiceContainer()->getDcaManager()->getFormatter($name ?: static::getName());
    }

    /**
     * Format a value.
     *
     * @param string             $name      Column name.
     * @param array|\ArrayAccess $data      Given data.
     * @param string|null        $tableName Name of the data container.
     *
     * @return array|null|string
     */
    protected function formatValue($name, $data, $tableName = null)
    {
        return $this->getFormatter($tableName)->formatValue($name, $data[$name]);
    }
}
