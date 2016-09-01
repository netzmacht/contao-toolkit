<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

use ArrayObject;
use Database\Result;
use Interop\Container\ContainerInterface as Container;
use Model;
use Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound;

/**
 * Class ComponentFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
final class ToolkitComponentFactory implements ComponentFactory
{
    /**
     * List of Component factories.
     *
     * @var ArrayObject|callable[]
     */
    private $factories;

    /**
     * Service container.
     *
     * @var Container
     */
    private $container;

    /**
     * ComponentFactory constructor.
     *
     * @param ArrayObject|callable[] $factories Component factories.
     * @param Container              $container Service container.
     */
    public function __construct(ArrayObject $factories, Container $container)
    {
        $this->factories = $factories;
        $this->container = $container;
    }

    /**
     * Create a new component.
     *
     * @param Model|Result $model  Component model.
     * @param string       $column Column in which the model is generated.
     *
     * @return mixed
     * @throws ComponentNotFound When no component factory is registered.
     */
    public function create($model, $column)
    {
        if (!isset($this->factories[$model->type])) {
            throw ComponentNotFound::forModel($model);
        }

        $component = call_user_func($this->factories[$model->type], $model, $column, $this->container);
        if (!$component instanceof Component) {
            throw ComponentNotFound::forModel($model);
        }

        return $component;
    }
}
