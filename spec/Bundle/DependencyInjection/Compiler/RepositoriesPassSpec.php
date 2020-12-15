<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RepositoriesPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RepositoriesPassSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(RepositoriesPass::class);
    }

    public function it_is_a_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_registeres_tagged_services_to_the_repository_manager(
        ContainerBuilder $container,
        Definition $definition
    ) {
        $taggedServices = [
            'foo' => [
                ['model' => 'FooModel']
            ],
            'bar' => [
                ['model' => 'BarModel']
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
