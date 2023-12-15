<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit;

use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\FosCacheResponseTaggerPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RegisterContaoModelPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RepositoriesPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\TemplateRendererPass;
use Netzmacht\Contao\Toolkit\NetzmachtContaoToolkitBundle;
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
        $container->addCompilerPass(Argument::type(RepositoriesPass::class))
            ->willReturn($container)
            ->shouldBeCalledOnce();

        $container->addCompilerPass(Argument::any())
            ->willReturn($container)
            ->shouldBeCalled();

        $this->build($container);
    }

    public function it_registers_contao_model_pass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(RegisterContaoModelPass::class))
            ->willReturn($container)
            ->shouldBeCalledOnce();

        $container->addCompilerPass(Argument::any())
            ->willReturn($container)
            ->shouldBeCalled();

        $this->build($container);
    }

    public function it_registers_fos_cache_response_tagger_pass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(FosCacheResponseTaggerPass::class))
            ->willReturn($container)
            ->shouldBeCalledOnce();

        $container->addCompilerPass(Argument::any())
            ->willReturn($container)
            ->shouldBeCalled();

        $this->build($container);
    }

    public function it_registers_template_renderer_pass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(TemplateRendererPass::class))
            ->willReturn($container)
            ->shouldBeCalledOnce();

        $container->addCompilerPass(Argument::not(Argument::type(TemplateRendererPass::class)))
            ->willReturn($container)
            ->shouldBeCalled();

        $this->build($container);
    }
}
