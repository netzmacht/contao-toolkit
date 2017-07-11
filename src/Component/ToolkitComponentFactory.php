<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Database\Result;
use Contao\Model;
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
     * @var ComponentFactory[]
     */
    private $factories;

    /**
     * ComponentFactory constructor.
     *
     * @param ComponentFactory[] $factories Component factories.
     */
    public function __construct($factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($model)
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($model)) {
                return true;
            }
        }

        return false;
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
        foreach ($this->factories as $factory) {
            if ($factory->supports($model)) {
                return $factory->create($model, $column);
            }
        }

        throw ComponentNotFound::forModel($model);
    }
}
