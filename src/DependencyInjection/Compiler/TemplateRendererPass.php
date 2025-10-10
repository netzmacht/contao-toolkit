<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\View\Template\DelegatingTemplateRenderer;
use Override;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TemplateRendererPass sets the twig environment if available.
 */
final class TemplateRendererPass implements CompilerPassInterface
{
    #[Override]
    public function process(ContainerBuilder $container): void
    {
        if (
            ! $container->hasDefinition('twig')
            || ! $container->hasDefinition('netzmacht.contao_toolkit.template_renderer')
        ) {
            return;
        }

        $definition = $container->getDefinition('netzmacht.contao_toolkit.template_renderer');
        if ($definition->getClass() !== DelegatingTemplateRenderer::class) {
            return;
        }

        $definition->replaceArgument(1, new Reference('twig'));
    }
}
