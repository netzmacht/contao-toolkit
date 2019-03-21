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
     * Name of the tag.
     *
     * @var string
     */
    private $tagName;

    /**
     * Index of the argument which should get the tagged references.
     *
     * @var int
     */
    private $argumentIndex;

    /**
     * ComponentFactoryCompilePass constructor.
     *
     * @param string $tagName       Name of the tag.
     * @param int    $argumentIndex Index of the argument which should get the tagged references.
     */
    public function __construct(string $tagName, int $argumentIndex)
    {
        $this->tagName       = $tagName;
        $this->argumentIndex = $argumentIndex;
    }

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
        $taggedServiceIds = $container->findTaggedServiceIds($this->tagName);
        $repositories     = (array) $definition->getArgument($this->argumentIndex);

        foreach ($taggedServiceIds as $tags) {
            foreach ($tags as $tag) {
                Assert::that($tag)->keyExists('model');
                Assert::that($tag['model'])->string();
                Assert::that($tag['model'])->classExists();
                Assert::that($tag['model'])->subclassOf(Model::class);

                /** @var Model $model */
                $model = $tag['model'];

                $repositories[$model::getTable()] = $model;
            }
        }

        $definition->replaceArgument($this->argumentIndex, $repositories);
    }
}
