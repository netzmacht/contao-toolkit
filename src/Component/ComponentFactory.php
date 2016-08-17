<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

use ArrayObject;
use Database\Result;
use Interop\Container\ContainerInterface as Container;
use Model;

/**
 * Class ComponentFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
final class ComponentFactory
{
    /**
     * Component factories.
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
     * @param Container  $container  Service container.
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
     * @throws ComponentNotFound
     */
    public function create($model, $column)
    {
        if (!isset($this->factories[$model->type])) {
            throw ComponentNotFound::forModel($model);
        }

        return call_user_func($this->factories[$model->type], $column, $this->container);
    }
}
