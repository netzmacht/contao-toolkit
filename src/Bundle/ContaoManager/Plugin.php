<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\ContaoManager;

use Composer\InstalledVersions;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Netzmacht\Contao\Toolkit\Bundle\NetzmachtContaoToolkitBundle;

use function version_compare;

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
                ->setLoadAfter([ContaoCoreBundle::class]),
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
        if (
            $extensionName !== 'framework'
            || ! version_compare((string) InstalledVersions::getVersion('symfony/framework-bundle'), '5.0', '<')
        ) {
            return $extensionConfigs;
        }

        // DEPRECATED: Enabling of the templating engine will be removed in version 4.0
        $extensionConfigs[] = [
            'templating' => [
                'engines' => ['toolkit'],
            ],
        ];

        return $extensionConfigs;
    }
}
