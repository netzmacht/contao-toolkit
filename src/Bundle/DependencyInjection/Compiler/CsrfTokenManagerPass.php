<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2019 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This class aliases the token manager which is used by Contao.
 */
final class CsrfTokenManagerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container) : void
    {
        if ($container->hasDefinition('contao.csrf.token_manager')) {
            $container->setAlias('netzmacht.contao_toolkit.csrf.token_manager', 'contao.csrf.token_manager');
        } else {
            $container->setAlias('netzmacht.contao_toolkit.csrf.token_manager', 'security.csrf.token_manager');
        }
    }
}
