<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2021 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\View\Template\DelegatingTemplateRenderer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TemplateRendererPass sets the twig environment if available.
 */
final class TemplateRendererPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig')
            || !$container->hasDefinition('netzmacht.contao_toolkit.template_renderer')
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
