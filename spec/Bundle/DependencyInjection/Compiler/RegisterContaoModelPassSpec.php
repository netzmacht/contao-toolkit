<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Contao\ContentModel;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RegisterContaoModelPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterContaoModelPassSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RegisterContaoModelPass::class);
    }

    public function it_is_a_compiler_pass(): void
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_registeres_models_to_contao_models(ContainerBuilder $container, Definition $definition): void
    {
        $taggedServices = [
            'content' => [
                ['model' => ContentModel::class],
            ],
            'module'  => [
                ['model' => ModuleModel::class],
            ],
        ];

        $definition->getArgument(0)->willReturn([])->shouldBeCalled();

        $container
            ->has('netzmacht.contao_toolkit.listeners.register_models')
            ->shouldBeCalled()
            ->willReturn(true);

        $container
            ->findDefinition('netzmacht.contao_toolkit.listeners.register_models')
            ->willReturn($definition);

        $container
            ->findTaggedServiceIds('netzmacht.contao_toolkit.repository')
            ->shouldBeCalled()
            ->willReturn($taggedServices);

        $definition->replaceArgument(0, Argument::type('array'))
            ->willReturn($definition)
            ->shouldBeCalled();

        $this->process($container);
    }
}
