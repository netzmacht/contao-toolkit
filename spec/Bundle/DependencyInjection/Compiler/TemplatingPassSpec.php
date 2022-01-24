<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\TemplatingPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class TemplatingPassSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(TemplatingPass::class);
    }

    public function it_sets_templating_alias_if_service_doesnt_not_exist(ContainerBuilder $container): void
    {
        $container->has('templating')
            ->willReturn(false);

        $container->setAlias('templating', 'templating.engine.toolkit')
            ->shouldBeCalled();

        $this->process($container);
    }

    public function it_doesnt_set_templating_alias_if_service_exist(ContainerBuilder $container): void
    {
        $container->has('templating')
            ->willReturn(true);

        $container->setAlias('templating', 'templating.engine.toolkit')
            ->shouldNotBeCalled();
    }
}
