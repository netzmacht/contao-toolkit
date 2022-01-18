<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function array_keys;

/** @deprecated Will be removed in version 3.0. Symfony has a built in support now */
final class AddTaggedServicesAsArgumentPass implements CompilerPassInterface
{
    /**
     * Service name which should be adjusted.
     *
     * @var string
     */
    private $serviceName;

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
     * @param string $serviceName   Service name which should be adjusted.
     * @param string $tagName       Name of the tag.
     * @param int    $argumentIndex Index of the argument which should get the tagged references.
     */
    public function __construct(string $serviceName, string $tagName, int $argumentIndex = 0)
    {
        $this->serviceName   = $serviceName;
        $this->tagName       = $tagName;
        $this->argumentIndex = $argumentIndex;
    }

    public function process(ContainerBuilder $container): void
    {
        if (! $container->has($this->serviceName)) {
            return;
        }

        $definition       = $container->findDefinition($this->serviceName);
        $taggedServiceIds = $container->findTaggedServiceIds($this->tagName);
        $services         = (array) $definition->getArgument($this->argumentIndex);

        foreach (array_keys($taggedServiceIds) as $serviceIds) {
            $services[] = new Reference($serviceIds);
        }

        $definition->replaceArgument($this->argumentIndex, $services);
    }
}
