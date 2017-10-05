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

namespace Netzmacht\Contao\Toolkit\DependencyInjection\CompilerPass;

use Netzmacht\Contao\Toolkit\Translation\LangArrayTranslator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * TranslatorCompilerPass registers a translator using the globals lang array used in Contao.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\CompilerPass
 */
class TranslatorCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('contao.translation.translator')) {
            return;
        }

        $definition = new Definition(
            LangArrayTranslator::class,
            [
                new Reference('contao.translation.translator.inner'),
                new Reference('contao.framework')
            ]
        );

        $definition->setDecoratedService('translator');
        $container->setDefinition('netzmacht.contao_toolkit.translation.translator', $definition);
    }
}
