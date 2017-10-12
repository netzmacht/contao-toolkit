<?php

namespace spec\Netzmacht\Contao\Toolkit\Bundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Netzmacht\Contao\Toolkit\Bundle\ContaoManager\Plugin;
use PhpSpec\ObjectBehavior;

class PluginSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Plugin::class);
    }

    function it_provides_bundle_config(ParserInterface $parser)
    {
        $this->shouldImplement(BundlePluginInterface::class);

        $this->getBundles($parser)->shouldBeArray();
    }

    function it_depends_on_core_bundle(ParserInterface $parser)
    {
        $this->shouldImplement(DependentPluginInterface::class);

        $this->getPackageDependencies()->shouldContain('contao/core-bundle');
        $this->getBundles($parser)[0]->getLoadAfter()->shouldContain(ContaoCoreBundle::class);
    }
}
