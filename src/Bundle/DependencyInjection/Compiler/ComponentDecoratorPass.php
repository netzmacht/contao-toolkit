<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Assertion\Assert;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ComponentDecoratorPass implements CompilerPassInterface
{
    /**
     * Name of the tag.
     *
     * @var string
     */
    private $tagName;

    /**
     * Index of the argument which should get the tagged references.
     *
     * @var int
     */
    private $argumentIndex;

    /**
     * Tag name of the factory to auto register the factory.
     *
     * @var string
     */
    private $factoryTagName;

    /**
     * @param string $tagName        Name of the tag.
     * @param int    $argumentIndex  Index of the argument which should get the tagged references.
     * @param string $factoryTagName Tag name of the factory to auto register the factory.
     */
    public function __construct(string $tagName, int $argumentIndex, string $factoryTagName)
    {
        $this->tagName        = $tagName;
        $this->argumentIndex  = $argumentIndex;
        $this->factoryTagName = $factoryTagName;
    }

    public function process(ContainerBuilder $container): void
    {
        $serviceId = 'netzmacht.contao_toolkit.listeners.register_component_decorators';

        if (! $container->has($serviceId)) {
            return;
        }

        $definition       = $container->findDefinition($serviceId);
        $taggedServiceIds = $container->findTaggedServiceIds($this->tagName);
        $components       = (array) $definition->getArgument($this->argumentIndex);

        foreach ($taggedServiceIds as $taggedServiceId => $tags) {
            foreach ($tags as $tag) {
                Assert::that($tag)->keyExists('category');
                Assert::that($tag['category'])->string();

                $key = isset($tag['alias']) ? 'alias' : 'type';
                Assert::that($tag)->keyExists($key);
                Assert::that($tag[$key])->string();

                $components[$tag['category']][] = $tag[$key];
            }

            $serviceDefinition = $container->getDefinition($taggedServiceId);
            if ($serviceDefinition->hasTag($this->factoryTagName)) {
                continue;
            }

            $serviceDefinition->addTag($this->factoryTagName);
        }

        $definition->replaceArgument($this->argumentIndex, $components);
    }
}
