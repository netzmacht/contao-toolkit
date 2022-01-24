<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Bundle\ContaoManager;

use Composer\InstalledVersions;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Netzmacht\Contao\Toolkit\Bundle\ContaoManager\Plugin;
use PhpSpec\ObjectBehavior;
use ReflectionProperty;

class PluginSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Plugin::class);
    }

    public function it_provides_bundle_config(ParserInterface $parser): void
    {
        $this->shouldImplement(BundlePluginInterface::class);

        $this->getBundles($parser)->shouldBeArray();
    }

    public function it_depends_on_core_bundle(ParserInterface $parser): void
    {
        $this->shouldImplement(DependentPluginInterface::class);

        $this->getPackageDependencies()->shouldContain('contao/core-bundle');
        $this->getBundles($parser)[0]->getLoadAfter()->shouldContain(ContaoCoreBundle::class);
    }

    public function it_configures_templating_engine_for_framework_44(ContainerBuilder $container): void
    {
        $reflection = new ReflectionProperty(InstalledVersions::class, 'canGetVendors');
        $reflection->setAccessible(true);
        $reflection->setValue(false);

        InstalledVersions::reload(['versions' => ['symfony/framework-bundle' => ['version' => '4.4.12']]]);

        $this->getExtensionConfig('framework', [], $container)
            ->shouldReturn([['templating' => ['engines' => ['toolkit']]]]);

        $reflection->setValue(null);
    }

    public function it_doesnt_configure_templating_engine_for_framework_5(ContainerBuilder $container): void
    {
        $reflection = new ReflectionProperty(InstalledVersions::class, 'canGetVendors');
        $reflection->setAccessible(true);
        $reflection->setValue(false);

        InstalledVersions::reload(['versions' => ['symfony/framework-bundle' => ['version' => '5.4.1']]]);

        $this->getExtensionConfig('framework', [], $container)
            ->shouldReturn([]);

        $reflection->setValue(null);
    }
}
