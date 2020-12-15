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
    public function it_is_initializable()
    {
        $this->shouldHaveType(RegisterContaoModelPass::class);
    }

    public function it_is_a_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_registeres_models_to_contao_models(ContainerBuilder $container, Definition $definition)
    {
        $taggedServices = [
            'content' => [
                ['model' => ContentModel::class]
            ],
            'module'  => [
                ['model' => ModuleModel::class]
            ]
        ];

        $definition->getArgument(0)->shouldBeCalled();

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

        $definition->replaceArgument(0, ['tl_content' => ContentModel::class, 'tl_module' => ModuleModel::class])
            ->shouldBeCalled();

        $this->process($container);
    }
}
