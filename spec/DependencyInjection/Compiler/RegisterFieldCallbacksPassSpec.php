<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RegisterFieldCallbacksPass;
use Netzmacht\Contao\Toolkit\Dca\Listener\RegisterFieldCallbacksListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class RegisterFieldCallbacksPassSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RegisterFieldCallbacksPass::class);
    }

    public function it_is_a_compiler_pass(): void
    {
        $this->shouldImplement(CompilerPassInterface::class);
    }

    public function it_does_nothing_if_the_listener_is_not_registered(ContainerBuilder $container): void
    {
        $container->hasDefinition(RegisterFieldCallbacksListener::class)->willReturn(false);
        $container->findTaggedServiceIds(Argument::any())->shouldNotBeCalled();

        $this->process($container);
    }

    public function it_builds_the_callback_map_from_tagged_services(
        ContainerBuilder $container,
        Definition $definition,
    ): void {
        $taggedServices = [
            'App\TemplateOptionsListener' => [
                ['key' => 'template_options', 'slot' => 'options_callback', 'method' => 'onOptionsCallback'],
            ],
            'App\SlugAliasListener' => [
                ['key' => 'alias_generator', 'slot' => 'save_callback', 'method' => 'onSaveCallback'],
            ],
        ];

        $container->hasDefinition(RegisterFieldCallbacksListener::class)->willReturn(true);
        $container->findTaggedServiceIds('netzmacht.contao_toolkit.dca.auto_callback')->willReturn($taggedServices);
        $container->getDefinition(RegisterFieldCallbacksListener::class)->willReturn($definition);

        $definition
            ->setArgument(2, [
                'template_options' => [
                    'slot' => 'options_callback',
                    'service' => 'App\TemplateOptionsListener',
                    'method' => 'onOptionsCallback',
                ],
                'alias_generator' => [
                    'slot' => 'save_callback',
                    'service' => 'App\SlugAliasListener',
                    'method' => 'onSaveCallback',
                ],
            ])
            ->shouldBeCalled()
            ->willReturn($definition);

        $this->process($container);
    }
}
