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

use Netzmacht\Contao\Toolkit\DependencyInjection\ContainerTrait;

/**
 * Base class for data container callback classes.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
abstract class Callbacks
{
    use ContainerTrait {
        getContainer as private;
    }

    /**
     * Name of the data container.
     *
     * @var string
     */
    protected static $name;

    /**
     * Data container manager.
     *
     * @var Manager
     */
    private $dcaManager;

    /**
     * Callbacks constructor.
     *
     * @param Manager $dcaManager Data container manager.
     */
    public function __construct(Manager $dcaManager)
    {
        $this->dcaManager = $dcaManager;
    }

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
     * @param string $name Callback method.
     *
     * @return callable
     */
    public static function callback($name)
    {
        return function () use ($name) {
            $serviceName = 'contao.dca.' . static::getName();
            $service     = static::getContainer()->get($serviceName);
            $callback    = [$service, $name];
            $arguments   = func_get_args();

            return call_user_func_array($callback, $arguments);
        };
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
        return $this->dcaManager->getDefinition($name ?: static::getName());
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
        return $this->dcaManager->getFormatter($name ?: static::getName());
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
