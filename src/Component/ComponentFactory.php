<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

use Database\Result;
use Model;
use Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound;

/**
 * Class ComponentFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
interface ComponentFactory
{
    /**
     * Create a new component.
     *
     * @param Model|Result $model  Component model.
     * @param string       $column Column in which the model is generated.
     *
     * @return mixed
     * @throws ComponentNotFound When no component factory is registered.
     */
    public function create($model, $column);
}
