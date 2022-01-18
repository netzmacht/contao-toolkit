<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\Module;

use Contao\Database\Result;
use Contao\Module;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;

use function assert;

/**
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress DeprecatedTrait
 */
final class ModuleDecorator extends Module
{
    use ComponentDecoratorTrait;

    /**
     * @param ModuleModel|Result $moduleModel
     */
    public function __construct($moduleModel, string $column = 'main')
    {
        $this->component = $this->getFactory()->create($moduleModel, $column);
    }

    /** @psalm-suppress DeprecatedClass */
    protected function getFactory(): ComponentFactory
    {
        $factory = self::getContainer()->get('netzmacht.contao_toolkit.component.frontend_module_factory');
        /** @psalm-suppress DeprecatedClass */
        assert($factory instanceof ComponentFactory);

        return $factory;
    }
}
