<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Bundle;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\FosCacheResponseTaggerPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RegisterContaoModelPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RepositoriesPass;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\TemplateRendererPass;
use Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class NetzmachtContaoToolkitBundleSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NetzmachtContaoToolkitBundle::class);
    }

    public function it_registers_repositories_pass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(RepositoriesPass::class))->shouldBeCalledOnce();
        $container->addCompilerPass(Argument::any())->shouldBeCalled();

        $this->build($container);
    }

    public function it_registers_contao_model_pass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(RegisterContaoModelPass::class))->shouldBeCalledOnce();
        $container->addCompilerPass(Argument::any())->shouldBeCalled();

        $this->build($container);
    }

    public function it_registers_fos_cache_response_tagger_pass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(FosCacheResponseTaggerPass::class))->shouldBeCalledOnce();
        $container->addCompilerPass(Argument::any())->shouldBeCalled();

        $this->build($container);
    }

    public function it_registers_template_renderer_pass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(TemplateRendererPass::class))->shouldBeCalledOnce();
        $container->addCompilerPass(Argument::not(Argument::type(TemplateRendererPass::class)))->shouldBeCalled();

        $this->build($container);
    }
}
