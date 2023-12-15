<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit;

use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\FosCacheResponseTaggerPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RegisterContaoModelPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\RepositoriesPass;
use Netzmacht\Contao\Toolkit\DependencyInjection\Compiler\TemplateRendererPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NetzmachtContaoToolkitBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RepositoriesPass());
        $container->addCompilerPass(new FosCacheResponseTaggerPass());
        $container->addCompilerPass(new RegisterContaoModelPass());
        $container->addCompilerPass(new TemplateRendererPass());
    }
}
