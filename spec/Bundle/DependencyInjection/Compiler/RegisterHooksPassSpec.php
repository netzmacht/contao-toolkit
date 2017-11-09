<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler\RegisterHooksPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterHooksPassSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RegisterHooksPass::class);
    }

    function it_should_not_process_if_no_listener_definition_exists(ContainerBuilder $container)
    {
        $container->hasDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->willReturn(false);

        $container->findTaggedServiceIds(Argument::any())->shouldNotBeCalled();

        $this->process($container);
    }

    function it_registered_tagged_hook_listeners(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->willReturn(true);

        $container->findTaggedServiceIds('contao.hook')
            ->willReturn(
                [
                    'foo' => [
                        [
                            'name'     => 'contao.hook',
                            'hook'     => 'parseTemplate',
                            'method'   => 'handleParseTemplate',
                            'priority' => 0,
                        ],
                    ],
                    'bar' => [
                        [
                            'name'     => 'contao.hook',
                            'hook'     => 'generatePage',
                            'method'   => 'handleGeneratePage',
                            'priority' => 0,
                        ],
                        [
                            'name'     => 'contao.hook',
                            'hook'     => 'replaceInsertTags',
                            'method'   => 'replaceInsertTags',
                            'priority' => 0,
                        ],
                    ],
                ]
            );

        $container->getDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->shouldBeCalled()
            ->willReturn($definition);

        $definition
            ->setArgument(
                1,
                [
                    'parseTemplate' => [
                        0 => [
                            ['foo', 'handleParseTemplate']
                        ]
                    ],
                    'generatePage' => [
                        0 => [
                            ['bar', 'handleGeneratePage']
                        ]
                    ],
                    'replaceInsertTags' => [
                        0 => [
                            ['bar', 'replaceInsertTags']
                        ]
                    ]
                ]
            )->shouldBeCalled();

        $this->process($container);
    }

    function it_sets_default_method_name_if_not_set(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->willReturn(true);

        $container->findTaggedServiceIds('contao.hook')
            ->willReturn(
                [
                    'foo' => [
                        [
                            'name'     => 'contao.hook',
                            'hook'     => 'parseTemplate',
                            'priority' => 0,
                        ],
                    ]
                ]
            );

        $container->getDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->shouldBeCalled()
            ->willReturn($definition);

        $definition
            ->setArgument(
                1,
                [
                    'parseTemplate' => [
                        0 => [
                            ['foo', 'onParseTemplate']
                        ]
                    ],
                ]
            )->shouldBeCalled();

        $this->process($container);
    }

    function it_sets_default_priority_if_not_set(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->willReturn(true);

        $container->findTaggedServiceIds('contao.hook')
            ->willReturn(
                [
                    'foo' => [
                        [
                            'name'     => 'contao.hook',
                            'hook'     => 'parseTemplate',
                        ],
                    ]
                ]
            );

        $container->getDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->shouldBeCalled()
            ->willReturn($definition);

        $definition
            ->setArgument(
                1,
                [
                    'parseTemplate' => [
                        0 => [
                            ['foo', 'onParseTemplate']
                        ]
                    ],
                ]
            )->shouldBeCalled();

        $this->process($container);
    }

    function it_orders_hook_by_priority(ContainerBuilder $container, Definition $definition)
    {
        $container->hasDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->willReturn(true);

        $container->findTaggedServiceIds('contao.hook')
            ->willReturn(
                [
                    'bar' => [
                        [
                            'name'     => 'contao.hook',
                            'hook'     => 'parseTemplate',
                            'priority' => -10
                        ],
                    ],
                    'foo' => [
                        [
                            'name'     => 'contao.hook',
                            'hook'     => 'parseTemplate',
                            'priority' => 0
                        ],
                    ],
                    'baz' => [
                        [
                            'name'     => 'contao.hook',
                            'hook'     => 'parseTemplate',
                            'priority' => 10
                        ],
                    ]
                ]
            );

        $container->getDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->shouldBeCalled()
            ->willReturn($definition);

        $definition
            ->setArgument(
                1,
                [
                    'parseTemplate' => [
                        10 => [
                            ['baz', 'onParseTemplate']
                        ],
                        0 => [
                            ['foo', 'onParseTemplate']
                        ],
                        -10 => [
                            ['bar', 'onParseTemplate']
                        ]
                    ],
                ]
            )->shouldBeCalled();

        $this->process($container);
    }

    function it_should_throw_an_exception_if_hook_attribute_is_missing(ContainerBuilder $container)
    {
        $container->hasDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')
            ->willReturn(true);

        $container->findTaggedServiceIds('contao.hook')
            ->willReturn(
                [
                    'foo' => [
                        [
                            'name'     => 'contao.hook',
                            'priority' => 0,
                        ],
                    ]
                ]
            );

        $this->shouldThrow(InvalidConfigurationException::class)->during('process', [$container]);
    }
}
