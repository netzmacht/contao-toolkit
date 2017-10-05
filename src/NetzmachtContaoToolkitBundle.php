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

use Netzmacht\Contao\Toolkit\DependencyInjection\CompilerPass\AddTaggedServicesAsArgumentCompilerPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\CompilerPass\TranslatorCompilerPass;
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

        $container->addCompilerPass(new TranslatorCompilerPass());

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentCompilerPass(
                'netzmacht.contao_toolkit.component.content_element_factory',
                'netzmacht.contao_toolkit.component.content_element_factory'
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentCompilerPass(
                'netzmacht.contao_toolkit.component.frontend_module_factory',
                'netzmacht.contao_toolkit.component.frontend_module_factory'
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentCompilerPass(
                'netzmacht.contao_toolkit.listeners.create-formatter-subscriber',
                'netzmacht.contao_toolkit.dca.formatter'
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentCompilerPass(
                'netzmacht.contao_toolkit.listeners.create-formatter-subscriber',
                'netzmacht.contao_toolkit.dca.pre-filter',
                1
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentCompilerPass(
                'netzmacht.contao_toolkit.listeners.create-formatter-subscriber',
                'netzmacht.contao_toolkit.dca.formatter.post-filter',
                2
            )
        );

        $container->addCompilerPass(
            new AddTaggedServicesAsArgumentCompilerPass(
                'netzmacht.contao_toolkit.insert_tag.replacer',
                'netzmacht.contao_toolkit.insert_tag.parser',
                1
            )
        );
    }
}
