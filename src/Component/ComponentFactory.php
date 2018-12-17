<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Database\Result;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound;

/**
 * Class ComponentFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
interface ComponentFactory
{
    /**
     * Check if factory supports the component.
     *
     * @param Model|Result $model Component model.
     *
     * @return bool
     */
    public function supports($model): bool;

    /**
     * Create a new component.
     *
     * @param Model|Result $model  Component model.
     * @param string       $column Column in which the model is generated.
     *
     * @return Component
     * @throws ComponentNotFound When no component factory is registered.
     */
    public function create($model, string $column): Component;
}
