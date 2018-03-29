<?php

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\TranslatorPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class TranslatorPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TranslatorPass::class);
    }

    function it_is_a_compiler_pass()
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    function it_creates_translator_definition_if_contao_translator_does_not_exists(ContainerBuilder $container, Definition $definition, ParameterBag $bag)
    {
        $container->has('translator')->willReturn(true);

        $container->findDefinition('translator')->willReturn($definition);
        $container->getParameterBag()->willReturn($bag);


        $container->hasDefinition('contao.translation.translator')->willReturn(false);
        $container->setDefinition(
            'netzmacht.contao_toolkit.translation.translator',
            Argument::type(Definition::class)
        )->shouldBeCalled();

        $this->process($container);
    }

    function it_does_not_process_if_contao_translator_exists(ContainerBuilder $container, Definition $definition, ParameterBag $bag)
    {
        $container->has('translator')->willReturn(true);

        $container->findDefinition('translator')->willReturn($definition);
        $container->getParameterBag()->willReturn($bag);

        $container->hasDefinition('contao.translation.translator')->willReturn(true);
        $container->setDefinition(
            'netzmacht.contao_toolkit.translation.translator',
            Argument::type(Definition::class)
        )->shouldNotBeCalled();

        $this->process($container);
    }
}
