<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

/**
 * Class FactoryToClassMapConverter.
 *
 * It converts the factory based components config by setting a decorator class and adding the factory to the factory
 * map.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
final class FactoryToClassMapConverter
{
    /**
     * Legacy class map.
     *
     * @var array
     */
    private $classMap;

    /**
     * Factory map.
     *
     * @var \ArrayObject
     */
    private $factoryMap;

    /**
     * Decorator class name.
     *
     * @var string
     */
    private $decoratorClass;

    /**
     * ConfigConverter constructor.
     *
     * @param array        $classMap       Legacy class map.
     * @param \ArrayObject $factoryMap     Factory map.
     * @param string       $decoratorClass Class of the decorator class.
     */
    public function __construct(array &$classMap, \ArrayObject $factoryMap, $decoratorClass)
    {
        $this->classMap       = &$classMap;
        $this->factoryMap     = $factoryMap;
        $this->decoratorClass = $decoratorClass;
    }

    /**
     * Convert the mappings.
     *
     * @return void
     */
    public function convert()
    {
        foreach ($this->classMap as $category => $classes) {
            foreach ($classes as $name => $class) {
                if (!is_callable($class)) {
                    continue;
                }

                $this->factoryMap[$name]          = $class;
                $this->classMap[$category][$name] = $this->decoratorClass;
            }
        }
    }
}
