<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit;

use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\AddTaggedServicesAsArgumentPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\ComponentDecoratorPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RegisterHooksPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\TranslatorPass;
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
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TranslatorPass());

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

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentPass(
                'netzmacht.contao_toolkit.insert_tag.replacer',
                'netzmacht.contao_toolkit.insert_tag_parser',
                1
            )
        );

        $container->addCompilerPass(
            new ComponentDecoratorPass('netzmacht.contao_toolkit.component.frontend_module', 0)
        );

        $container->addCompilerPass(
            new ComponentDecoratorPass('netzmacht.contao_toolkit.component.content_element', 1)
        );

        // Contao 4.5 will support tagged hook listeners out of the box if PR got merged
        // https://github.com/contao/core-bundle/pull/1094/files
        if (!class_exists('Contao\CoreBundle\DependencyInjection\Compiler\RegisterHooksPass')) {
            $container->addCompilerPass(new RegisterHooksPass());
        }
    }
}
