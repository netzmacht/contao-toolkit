<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Christopher Bölter <christopher@boelter.eu>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\AddTaggedServicesAsArgumentPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\ComponentDecoratorPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\CsrfTokenManagerPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\FosCacheResponseTaggerPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RegisterContaoModelPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RepositoriesPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\TemplateRendererPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NetzmachtContaoToolkitBundle.
 *
 * @package Netzmacht\Contao\Toolkit
 */
final class NetzmachtContaoToolkitBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RepositoriesPass());
        $container->addCompilerPass(new FosCacheResponseTaggerPass());

        $container->addCompilerPass(
            new ComponentDecoratorPass(
                'netzmacht.contao_toolkit.component.frontend_module',
                0,
                'netzmacht.contao_toolkit.component.frontend_module_factory'
            )
        );

        $container->addCompilerPass(
            new ComponentDecoratorPass(
                'netzmacht.contao_toolkit.component.content_element',
                1,
                'netzmacht.contao_toolkit.component.content_element_factory'
            )
        );

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
    }
}
