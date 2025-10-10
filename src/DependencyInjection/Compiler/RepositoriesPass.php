<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Override;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function sprintf;

final class RepositoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws RuntimeException When table attribute is not given for a tagged repository.
     */
    #[Override]
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition('netzmacht.contao_toolkit.repository_manager')) {
            return;
        }

        $definition   = $container->getDefinition('netzmacht.contao_toolkit.repository_manager');
        $services     = $container->findTaggedServiceIds('netzmacht.contao_toolkit.repository');
        $repositories = (array) $definition->getArgument(1);

        foreach ($services as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (! isset($attributes['model'])) {
                    throw new RuntimeException(
                        sprintf('Service "%s" is tagged as repository but has no model attribute', $serviceId),
                    );
                }

                $repositories[$attributes['model']] = new Reference($serviceId);
            }
        }

        $definition->setArgument(1, $repositories);
    }
}
