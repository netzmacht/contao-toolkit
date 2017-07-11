<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ComponentFactoryCompilePass.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\CompilerPass
 */
class ComponentFactoryCompilePass implements CompilerPassInterface
{
    /**
     * Service name which should be adjusted.
     *
     * @var string
     */
    private $serviceName;

    /**
     * Name of the tag.
     *
     * @var string
     */
    private $tagName;

    /**
     * Index of the argument which should get the tagged references.
     *
     * @var int
     */
    private $argumentIndex;

    /**
     * ComponentFactoryCompilePass constructor.
     *
     * @param string $serviceName   Service name which should be adjusted.
     * @param string $tagName       Name of the tag.
     * @param int    $argumentIndex Index of the argument which should get the tagged references.
     */
    public function __construct($serviceName, $tagName, $argumentIndex = 0)
    {
        $this->serviceName   = $serviceName;
        $this->tagName       = $tagName;
        $this->argumentIndex = $argumentIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has($this->serviceName)) {
            return;
        }

        $definition = $container->findDefinition($this->serviceName);
        $taggedServiceIds = $container->findTaggedServiceIds($this->tagName);
        $services         = (array) $definition->getArgument($this->argumentIndex);

        foreach (array_keys($taggedServiceIds) as $serviceIds) {
            $services[] = new Reference($serviceIds);
        }

        $definition->replaceArgument($this->argumentIndex, $services);
    }
}
