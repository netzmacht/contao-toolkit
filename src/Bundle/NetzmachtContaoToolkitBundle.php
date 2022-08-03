<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\AddTaggedServicesAsArgumentPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\FosCacheResponseTaggerPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RegisterContaoModelPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RepositoriesPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\TemplateRendererPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\TemplatingPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/** @psalm-suppress DeprecatedClass */
final class NetzmachtContaoToolkitBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RepositoriesPass());
        $container->addCompilerPass(new FosCacheResponseTaggerPass());

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_toolkit.component.content_element_factory',
                'netzmacht.contao_toolkit.component.content_element_factory'
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_toolkit.component.frontend_module_factory',
                'netzmacht.contao_toolkit.component.frontend_module_factory'
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_toolkit.listeners.create_formatter_subscriber',
                'netzmacht.contao_toolkit.dca.formatter'
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_toolkit.listeners.create_formatter_subscriber',
                'netzmacht.contao_toolkit.dca.formatter.pre_filter',
                1
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_toolkit.listeners.create_formatter_subscriber',
                'netzmacht.contao_toolkit.dca.formatter.post_filter',
                2
            )
        );

        $container->addCompilerPass(new RegisterContaoModelPass());
        $container->addCompilerPass(new TemplateRendererPass());
        $container->addCompilerPass(new TemplatingPass());
    }
}
