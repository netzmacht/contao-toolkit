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

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Find hook services and store them in an parameter.
 */
class RegisterHooksPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners')) {
            return;
        }

        $serviceIds = $container->findTaggedServiceIds('contao.hook');
        $hooks      = [];

        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $this->guardRequiredAttributesExist($serviceId, $attributes);

                $priority = (int) ($attributes['priority'] ?? 0);
                $hook     = $attributes['hook'];

                $hooks[$hook][$priority][] = [$serviceId, $attributes['method']];
            }
        }

        if (count($hooks) > 0) {
            // Apply priority sorting.
            foreach (array_keys($hooks) as $hook) {
                krsort($hooks[$hook]);
            }

            $definition = $container->getDefinition('netzmacht.contao_toolkit.listeners.merge_hook_listeners');
            $definition->setArgument(1, $hooks);
        }
    }

    /**
     * Guard that required attributes (hook and method) are defined.
     *
     * @param string $serviceId  Service id.
     * @param array  $attributes Tag attributes.
     *
     * @return void
     *
     * @throws InvalidConfigurationException When an attribute is missing.
     */
    private function guardRequiredAttributesExist(string $serviceId, array $attributes): void
    {
        if (!isset($attributes['hook'])) {
            throw new InvalidConfigurationException(
                sprintf('Missing hook attribute in tagged hook service with service id "%s"', $serviceId)
            );
        }

        if (!isset($attributes['method'])) {
            throw new InvalidConfigurationException(
                sprintf('Missing method attribute in tagged hook service with service id "%s"', $serviceId)
            );
        }
    }
}
