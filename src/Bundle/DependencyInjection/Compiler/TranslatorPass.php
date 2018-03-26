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

use Netzmacht\Contao\Toolkit\Translation\LangArrayTranslator;
use Netzmacht\Contao\Toolkit\Translation\LangArrayTranslatorBagTranslator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Translation\TranslatorBagInterface as TranslatorBag;

/**
 * TranslatorCompilerPass registers a translator using the globals lang array used in Contao.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection\CompilerPass
 */
final class TranslatorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('translator')) {
            return;
        }

        if ($container->hasDefinition('contao.translation.translator')) {
            return;
        }

        $definition      = $container->findDefinition('translator');
        $translatorClass = $container->getParameterBag()->resolveValue($definition->getClass());
        $decoratorClass  = is_subclass_of($translatorClass, TranslatorBag::class)
            ? LangArrayTranslatorBagTranslator::class
            : LangArrayTranslator::class;

        $definition = new Definition(
            $decoratorClass,
            [
                new Reference('netzmacht.contao_toolkit.translation.translator.inner'),
                new Reference('contao.framework')
            ]
        );

        $definition->setDecoratedService('translator');
        $container->setDefinition('netzmacht.contao_toolkit.translation.translator', $definition);
    }
}
