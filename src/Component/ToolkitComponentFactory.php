<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component;

use Contao\Database\Result;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Component\Exception\ComponentNotFound;

/**
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * @psalm-suppress DeprecatedInterface
 */
final class ToolkitComponentFactory implements ComponentFactory
{
    // phpcs:disable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectAnnotationsGroup
    /**
     * List of Component factories.
     *
     * @psalm-suppress DeprecatedClass
     * @psalm-suppress DeprecatedInterface
     *
     * @var ComponentFactory[]
     */
    private $factories;

    // phpcs:enable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectAnnotationsGroup
    // phpcs:disable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectOrderOfAnnotationsGroup

    /**
     * @psalm-suppress DeprecatedClass
     *
     * @param array|ComponentFactory[] $factories Component factories.
     */
    public function __construct(array $factories)
    {
        $this->factories = $factories;
    }

    // phpcs:enable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectOrderOfAnnotationsGroup

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
     * @throws ComponentNotFound When no component factory is registered.
     *
     * @psalm-suppress DeprecatedClass
     */
    public function create($model, string $column): Component
    {
        foreach ($this->factories as $factory) {
            if ($factory->supports($model)) {
                return $factory->create($model, $column);
            }
        }

        /** @psalm-suppress DeprecatedClass */
        throw ComponentNotFound::forModel($model);
    }
}
