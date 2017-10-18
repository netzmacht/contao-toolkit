<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RepositoriesPass
 *
 * @package Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler
 */
class RepositoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException When table attribute is not given for a tagged repository.
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('netzmacht.contao_toolkit.repository_manager')) {
            return;
        }

        $definition   = $container->getDefinition('netzmacht.contao_toolkit.repository_manager');
        $services     = $container->findTaggedServiceIds('netzmacht.contao_toolkit.repository');
        $repositories = (array) $definition->getArgument(0);

        foreach ($services as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['table'])) {
                    throw new RuntimeException(
                        sprintf('Service "%s" is tagged as repository but has no table attribute', $serviceId)
                    );
                }

                $repositories[$attributes['table']] = new Reference($serviceId);
            }
        }

        $definition->setArgument(0, $repositories);
    }
}
