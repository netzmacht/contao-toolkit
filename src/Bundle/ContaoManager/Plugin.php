<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle;

/**
 * Class Plugin.
 *
 * @package Netzmacht\Contao\Toolkit\ContaoManager
 */
final class Plugin implements BundlePluginInterface, ExtensionPluginInterface, DependentPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(NetzmachtContaoToolkitBundle::class)
                ->setReplace(['toolkit'])
                ->setLoadAfter([ContaoCoreBundle::class])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageDependencies(): array
    {
        return ['contao/core-bundle'];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container): array
    {
        if ($extensionName !== 'framework') {
            return $extensionConfigs;
        }

        $extensionConfigs[] = [
            'templating' => [
                'engines' => ['toolkit']
            ]
        ];

        return $extensionConfigs;
    }
}
