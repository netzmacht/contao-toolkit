<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Database\Result;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound;

/**
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 */
interface ComponentFactory
{
    /**
     * Check if factory supports the component.
     *
     * @param Model|Result $model Component model.
     */
    public function supports($model): bool;

    /**
     * Create a new component.
     *
     * @param Model|Result $model  Component model.
     * @param string       $column Column in which the model is generated.
     *
     * @throws ComponentNotFound When no component factory is registered.
     *
     * @psalm-suppress DeprecatedClass
     */
    public function create($model, string $column): Component;
}
