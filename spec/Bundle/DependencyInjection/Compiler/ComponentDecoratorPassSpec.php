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

    private const FACTORY_TAG = 'foo.factory';

    private const DECORATOR_SERVICE = 'netzmacht.contao_toolkit.listeners.register_component_decorators';

    public function let(): void
    {
        $this->beConstructedWith(self::TAG, self::ARGUMENT_INDEX, self::FACTORY_TAG);
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

        $container->getDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn([self::DECORATOR_SERVICE => [['category' => 'foo', 'alias' => 'bar']]]);

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

        $container->getDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn([self::DECORATOR_SERVICE=> [['category' => 'foo', 'type' => 'bar']]]);

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

        $container->getDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $definition->getArgument(self::ARGUMENT_INDEX)
            ->shouldBeCalled()
            ->willReturn(['foo' => ['baz']]);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn([self::DECORATOR_SERVICE => [['category' => 'foo', 'type' => 'bar']]]);

        $definition->hasTag(self::FACTORY_TAG)
            ->shouldBeCalled()
            ->willReturn(true);

        $definition->replaceArgument(self::ARGUMENT_INDEX, ['foo' => ['baz', 'bar']])
            ->shouldBeCalled();

        $this->process($container);
    }

    public function it_adds_factory_tag_if_not_exists(ContainerBuilder $container, Definition $definition): void
    {
        $container->has(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $container->getDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $definition->getArgument(self::ARGUMENT_INDEX)
            ->shouldBeCalled()
            ->willReturn(['foo' => ['baz']]);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn([self::DECORATOR_SERVICE => [['category' => 'foo', 'type' => 'bar']]]);

        $definition->hasTag(self::FACTORY_TAG)
            ->shouldBeCalled()
            ->willReturn(false);

        $definition->addTag(self::FACTORY_TAG)
            ->shouldBeCalledOnce();

        $definition->replaceArgument(self::ARGUMENT_INDEX, ['foo' => ['baz', 'bar']])
            ->shouldBeCalled();

        $this->process($container);
    }


    public function it_doesnt_add_factory_tag_if_already_exists(
        ContainerBuilder $container,
        Definition $definition
    ): void {
        $container->has(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn(true);

        $container->findDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $container->getDefinition(self::DECORATOR_SERVICE)
            ->shouldBeCalled()
            ->willReturn($definition);

        $definition->getArgument(self::ARGUMENT_INDEX)
            ->shouldBeCalled()
            ->willReturn(['foo' => ['baz']]);

        $container->findTaggedServiceIds(self::TAG)
            ->shouldBeCalled()
            ->willReturn([self::DECORATOR_SERVICE => [['category' => 'foo', 'type' => 'bar']]]);

        $definition->hasTag(self::FACTORY_TAG)
            ->shouldBeCalled()
            ->willReturn(true);

        $definition->addTag(self::FACTORY_TAG)
            ->shouldNotBeCalled();

        $definition->replaceArgument(self::ARGUMENT_INDEX, ['foo' => ['baz', 'bar']])
            ->shouldBeCalled();

        $this->process($container);
    }
}
