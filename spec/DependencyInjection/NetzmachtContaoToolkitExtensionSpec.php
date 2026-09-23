<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\DependencyInjection;

use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Data\Updater\Updater;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Netzmacht\Contao\Toolkit\DependencyInjection\NetzmachtContaoToolkitExtension;
use Netzmacht\Contao\Toolkit\Security\Csrf\CsrfTokenProvider;
use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use function expect;

final class NetzmachtContaoToolkitExtensionSpec extends ObjectBehavior
{
    private const ALIASES = [
        DcaManager::class        => 'netzmacht.contao_toolkit.dca.manager',
        Invoker::class           => 'netzmacht.contao_toolkit.callback_invoker',
        RepositoryManager::class => 'netzmacht.contao_toolkit.repository_manager',
        Updater::class           => 'netzmacht.contao_toolkit.data.database_row_updater',
        TemplateRenderer::class  => 'netzmacht.contao_toolkit.template_renderer',
        AssetsManager::class     => 'netzmacht.contao_toolkit.assets_manager',
        CsrfTokenProvider::class => 'netzmacht.contao_toolkit.csrf.token_provider',
        FormatterFactory::class  => 'netzmacht.contao_toolkit.dca.formatter.factory',
    ];

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NetzmachtContaoToolkitExtension::class);
    }

    public function it_registers_autowiring_aliases(): void
    {
        $container = new ContainerBuilder();

        $this->load([], $container);

        foreach (self::ALIASES as $alias => $serviceId) {
            expect($container->hasAlias($alias))->shouldReturn(true);
            expect((string) $container->getAlias($alias))->shouldReturn($serviceId);
            expect($container->getAlias($alias)->isPublic())->shouldReturn(false);
            expect($container->hasDefinition($serviceId))->shouldReturn(true);
        }
    }
}
