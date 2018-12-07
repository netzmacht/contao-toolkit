<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\AddTaggedServicesAsArgumentPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\ComponentDecoratorPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\FosCacheResponseTaggerPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RegisterHooksPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RepositoriesPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\TranslatorPass;
use PackageVersions\Versions;
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
     * Contao core version.
     *
     * @var string
     */
    private $contaoCoreVersion;

    /**
     * NetzmachtContaoToolkitBundle constructor.
     *
     * @param null|string $contaoCoreVersion Contao core version. Available for testing purposes.
     */
    public function __construct(?string $contaoCoreVersion = null)
    {
        if (!$contaoCoreVersion) {
            $contaoCoreVersion = Versions::getVersion('contao/core-bundle');
            $contaoCoreVersion = explode('@', $contaoCoreVersion, 1)[0];
        }

        $this->contaoCoreVersion = $contaoCoreVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new TranslatorPass());
        $container->addCompilerPass(new RepositoriesPass());
        $container->addCompilerPass(new FosCacheResponseTaggerPass($this->contaoCoreVersion));

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
            new ComponentDecoratorPass('netzmacht.contao_toolkit.component.frontend_module', 0)
        );

        $container->addCompilerPass(
            new ComponentDecoratorPass('netzmacht.contao_toolkit.component.content_element', 1)
        );

        // Since Contao 4.5 tagged hook listeners are supported by the Contao core
        if (version_compare($this->contaoCoreVersion, '4.5', '<')) {
            $container->addCompilerPass(new RegisterHooksPass());
        }
    }
}
