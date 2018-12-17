<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
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
final class RepositoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException When table attribute is not given for a tagged repository.
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('netzmacht.contao_toolkit.repository_manager')) {
            return;
        }

        $definition   = $container->getDefinition('netzmacht.contao_toolkit.repository_manager');
        $services     = $container->findTaggedServiceIds('netzmacht.contao_toolkit.repository');
        $repositories = (array) $definition->getArgument(1);

        foreach ($services as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                if (!isset($attributes['model'])) {
                    throw new RuntimeException(
                        sprintf('Service "%s" is tagged as repository but has no model attribute', $serviceId)
                    );
                }

                $repositories[$attributes['model']] = new Reference($serviceId);
            }
        }

        $definition->setArgument(1, $repositories);
    }
}
