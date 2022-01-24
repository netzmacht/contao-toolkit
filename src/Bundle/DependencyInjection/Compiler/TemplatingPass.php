<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Provides falback for the templating service not available in Symfony 5 anymore.
 *
 * @deprecated Will be removed in version 4.0
 */
final class TemplatingPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('templating')) {
            return;
        }

        $container->setAlias('templating', 'templating.engine.toolkit');
    }
}
