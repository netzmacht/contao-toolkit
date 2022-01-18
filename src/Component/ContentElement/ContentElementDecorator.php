<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\Database\Result;
use Netzmacht\Contao\Toolkit\Component\ComponentDecoratorTrait;
use Netzmacht\Contao\Toolkit\Component\ComponentFactory;

use function assert;

/**
 * @deprecated Since 3.5.0 and get removed in 4.0.0
 *
 * @psalm-suppress PropertyNotSetInConstructor
 * @psalm-suppress DeprecatedTrait
 */
final class ContentElementDecorator extends ContentElement
{
    use ComponentDecoratorTrait;

    /**
     * @param ContentModel|Result $contentModel
     */
    public function __construct($contentModel, string $column = 'main')
    {
        $this->component = $this->getFactory()->create($contentModel, $column);
    }

    /** @psalm-suppress DeprecatedClass */
    protected function getFactory(): ComponentFactory
    {
        $factory = $this->getContainer()->get('netzmacht.contao_toolkit.component.content_element_factory');
        /** @psalm-suppress DeprecatedClass */
        assert($factory instanceof ComponentFactory);

        return $factory;
    }
}
