<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Dca\Listener\RegisterFieldCallbacksListener;
use Override;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface as CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Collects services tagged with "netzmacht.contao_toolkit.dca.auto_callback" into the config map
 * consumed by RegisterFieldCallbacksListener.
 */
final class RegisterFieldCallbacksPass implements CompilerPass
{
    private const TAG = 'netzmacht.contao_toolkit.dca.auto_callback';

    #[Override]
    public function process(ContainerBuilder $container): void
    {
        if (! $container->hasDefinition(RegisterFieldCallbacksListener::class)) {
            return;
        }

        $callbacks = [];

        foreach ($container->findTaggedServiceIds(self::TAG) as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $callbacks[$attributes['key']] = [
                    'slot' => $attributes['slot'],
                    'service' => $serviceId,
                    'method' => $attributes['method'],
                ];
            }
        }

        $container->getDefinition(RegisterFieldCallbacksListener::class)->setArgument(2, $callbacks);
    }
}
