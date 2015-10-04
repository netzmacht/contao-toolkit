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

use Netzmacht\Contao\Toolkit\TranslatorTrait;

/**
 * Base class for data container callback classes.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
abstract class Callbacks
{
    use TranslatorTrait;

    /**
     * Name of the data container.
     *
     * @var string
     */
    protected $name;

    /**
     * Generate the callback definition.
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
    protected function getDefinition($name = null)
    {
        $this->getServiceContainer()->getDcaManager()->getDefinition($name ?: $this->name);
    }

    /**
     * Get a formatter.
     *
     * @param string|null $name Data definition name. If empty the default name is used.
     *
     * @return Formatter\Formatter
     */
    protected function getFormatter($name = null)
    {
        return $this->getServiceContainer()->getDcaManager()->getFormatter($name ?: $this->name);
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
