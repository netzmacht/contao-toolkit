<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Response\FosCacheResponseTagger;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface as CompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class FosCacheResponseTaggerPass registers the FosCacheResponseTagger if it's supported.
 *
 * The response tagger is supported since Contao 4.6 if the fos http cache is installed and enabled.
 */
final class FosCacheResponseTaggerPass implements CompilerPass
{
    private const SERVICE_ID = 'fos_http_cache.http.symfony_response_tagger';

    public function process(ContainerBuilder $container): void
    {
        if (! $container->has(self::SERVICE_ID)) {
            return;
        }

        $definition = new Definition(FosCacheResponseTagger::class);
        $definition->addArgument(new Reference(self::SERVICE_ID));

        $container->setDefinition('netzmacht.contao_toolkit.response_tagger', $definition);
    }
}
