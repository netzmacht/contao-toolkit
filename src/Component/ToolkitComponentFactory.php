<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
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
 * @deprecated Since 3.5.0 and get removed in 4.0.0
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
     * @param array|ComponentFactory[] $factories Component factories.
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($model): bool
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
     * @return Component
     * @throws ComponentNotFound When no component factory is registered.
     */
    public function create($model, string $column): Component
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($model)) {
                return $factory->create($model, $column);
            }
        }

        throw ComponentNotFound::forModel($model);
    }
}
