<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\Assert;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function assert;
use function is_subclass_of;

final class RegisterContaoModelPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $serviceId = 'netzmacht.contao_toolkit.listeners.register_models';

        if (! $container->has($serviceId)) {
            return;
        }

        $definition       = $container->findDefinition($serviceId);
        $taggedServiceIds = $container->findTaggedServiceIds('netzmacht.contao_toolkit.repository');
        $repositories     = (array) $definition->getArgument(0);

        foreach ($taggedServiceIds as $tags) {
            foreach ($tags as $tag) {
                Assert::that($tag)->keyExists('model');
                Assert::that($tag['model'])
                    ->string()
                    ->classExists()
                    ->subclassOf(Model::class);

                $model = $tag['model'];
                assert(is_subclass_of($model, Model::class, true));

                $repositories[$model::getTable()] = $model;
            }
        }

        $definition->replaceArgument(0, $repositories);
    }
}
