<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Netzmacht\Contao\Toolkit\ContaoManager\Plugin;
use PhpSpec\ObjectBehavior;

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
}
