<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ComponentFactoryCompilePass.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\CompilerPass
 */
final class AddTaggedServicesAsArgumentPass implements CompilerPassInterface
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
    public function __construct(string $serviceName, string $tagName, int $argumentIndex = 0)
    {
        $this->serviceName   = $serviceName;
        $this->tagName       = $tagName;
        $this->argumentIndex = $argumentIndex;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has($this->serviceName)) {
            return;
        }

        $definition       = $container->findDefinition($this->serviceName);
        $taggedServiceIds = $container->findTaggedServiceIds($this->tagName);
        $services         = (array) $definition->getArgument($this->argumentIndex);

        foreach (array_keys($taggedServiceIds) as $serviceIds) {
            $services[] = new Reference($serviceIds);
        }

        $definition->replaceArgument($this->argumentIndex, $services);
    }
}
