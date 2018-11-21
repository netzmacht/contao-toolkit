<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Contao\ManagerPlugin\Config\ContainerBuilder;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\ComponentDecoratorPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Definition;

final class ComponentDecoratorPassSpec extends ObjectBehavior
{
    private const TAG = 'foo.bar';

    private const ARGUMENT_INDEX = 1;

    private const DECORATOR_SERVICE = 'netzmacht.contao_toolkit.listeners.register_component_decorators';

    public function let(): void
    {
        $this->beConstructedWith(self::TAG, self::ARGUMENT_INDEX);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ComponentDecoratorPass::class);
    }

    public function it_doesnt_find_tagged_service_if_decorator_service_not_exists(ContainerBuilder $container): void
    {
        $container->has(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn(false);

        $container->findDefinition(self::DECORATOR_SERVICE)
            ->shouldNotBeCalled();

        $this->process($container);
    }

    public function it_recognizes_missing_category_for_tagged_service(
        ContainerBuilder $container,
        Definition $definition
    ): void {
        $container->has(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn(['service' => [['alias' => 'foo']]]);

        $this->shouldThrow(AssertionFailed::class)->during('process', [$container]);
    }

    public function it_recognizes_missing_alias_for_tagged_service(
        ContainerBuilder $container,
        Definition $definition
    ): void {
        $container->has(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn(['service' => [['category' => 'foo']]]);

        $this->shouldThrow(AssertionFailed::class)->during('process', [$container]);
    }

    public function it_requires_alias_tag_attribute(ContainerBuilder $container, Definition $definition): void
    {
        $container->has(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn(['service' => [['category' => 'foo', 'alias' => 'bar']]]);

        $this->process($container);
    }

    public function it_supports_type_attribute_as_alternative_to_alias(
        ContainerBuilder $container,
        Definition $definition
    ): void {
        $container->has(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn(['service' => [['category' => 'foo', 'type' => 'bar']]]);

        $this->process($container);
    }

    public function it_extends_definition_argument(ContainerBuilder $container, Definition $definition): void
    {
        $container->has(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $definition->getArgument(self::ARGUMENT_INDEX)
            ->shouldBeCalled()
            ->willReturn(['foo' => ['baz']]);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn(['service' => [['category' => 'foo', 'type' => 'bar']]]);

        $definition->replaceArgument(self::ARGUMENT_INDEX, ['foo' => ['baz', 'bar']])
            ->shouldBeCalled();

        $this->process($container);
    }
}
