<?php

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RepositoriesPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RepositoriesPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RepositoriesPass::class);
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_registeres_tagged_services_to_the_repository_manager(ContainerBuilder $container, Definition $definition)
    {
        $taggedServices = [
            'foo' => [
                ['table' => 'tl_foo']
            ],
            'bar' => [
                ['table' => 'tl_bar']
            ]
        ];

        $definition->getArgument(1)->shouldBeCalled();

        $container
            ->hasDefinition('netzmacht.contao_toolkit.repository_manager')
            ->shouldBeCalled()
            ->willReturn(true);

        $container
            ->getDefinition('netzmacht.contao_toolkit.repository_manager')
            ->willReturn($definition);

        $container
            ->findTaggedServiceIds('netzmacht.contao_toolkit.repository')
            ->shouldBeCalled()
            ->willReturn($taggedServices);

        $definition->setArgument(1, Argument::size(2))->shouldBeCalled();

        $this->process($container);
    }
}
