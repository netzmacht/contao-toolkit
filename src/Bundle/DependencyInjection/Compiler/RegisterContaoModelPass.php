<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     Christopher Bölter <christopher@boelter.eu>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\Assert;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class RegisterContaoModelPass.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\Compiler
 */
final class RegisterContaoModelPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $serviceId = 'netzmacht.contao_toolkit.listeners.register_models';
        
        if (!$container->has($serviceId)) {
            return;
        }

        $definition       = $container->findDefinition($serviceId);
        $taggedServiceIds = $container->findTaggedServiceIds('netzmacht.contao_toolkit.repository');
        $repositories     = (array) $definition->getArgument(0);

        foreach ($taggedServiceIds as $tags) {
            foreach ($tags as $tag) {
                Assert::that($tag)->keyExists('model');
                Assert::that($tag['model'])
                    ->string()
                    ->classExists()
                    ->subclassOf(Model::class);

                /** @var Model $model */
                $model = $tag['model'];

                $repositories[$model::getTable()] = $model;
            }
        }

        $definition->replaceArgument(0, $repositories);
    }
}
