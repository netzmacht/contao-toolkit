<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Netzmacht\Contao\Toolkit\NetzmachtContaoToolkitBundle;
use Override;

final class Plugin implements BundlePluginInterface, DependentPluginInterface
{
    /** {@inheritDoc} */
    #[Override]
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(NetzmachtContaoToolkitBundle::class)
                ->setReplace(['toolkit'])
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }

    /** {@inheritDoc} */
    #[Override]
    public function getPackageDependencies(): array
    {
        return ['contao/core-bundle'];
    }
}
