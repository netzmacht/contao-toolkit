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

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Save;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Webmozart\Assert\Assert;

/**
 * Class GenerateAliasCallback is designed to create an alias of a column.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
final class GenerateAliasListener
{
    /**
     * Dependency container.
     *
     * @var Container
     */
    private $container;

    /**
     * Data container manager.
     *
     * @var Manager
     */
    private $dcaManager;

    /**
     * Default alias generator service id.
     *
     * @var string
     */
    private $defaultServiceId;

    /**
     * Construct.
     *
     * @param Container $container        Dependency container.
     * @param Manager   $dcaManager       Data container manager.
     * @param string    $defaultServiceId Default alias generator service id.
     */
    public function __construct(Container $container, Manager $dcaManager, $defaultServiceId)
    {
        $this->container        = $container;
        $this->defaultServiceId = $defaultServiceId;
        $this->dcaManager       = $dcaManager;
    }

    /**
     * Generate the alias value.
     *
     * @param mixed         $value         The current value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return mixed|null|string
     */
    public function handleSaveCallback($value, $dataContainer)
    {
        Assert::isInstanceOf($dataContainer, 'DataContainer');
        Assert::isInstanceOf($dataContainer->activeRecord, 'Database\Result');

        $serviceId = $this->getServiceId($dataContainer);
        $generator = $this->container->get($serviceId);

        $this->guardIsAliasGenerator($generator, $serviceId);

        return $generator->generate($dataContainer->activeRecord, $value);
    }

    /**
     * Get the service id.
     *
     * @param DataContainer $dataContainer Data container.
     *
     * @return string
     */
    private function getServiceId($dataContainer): string
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);
        $serviceId  = $definition->get(
            ['fields', $dataContainer->field, 'toolkit', 'alias_generator', 'serviceId'],
            $this->defaultServiceId
        );

        return $serviceId;
    }

    /**
     * Guard that service is an alias generator.
     *
     * @param mixed  $generator Retrieved alias generator service.
     * @param string $serviceId Service id.
     *
     * @return void
     */
    private function guardIsAliasGenerator($generator, string $serviceId): void
    {
        Assert::isInstanceOf(
            $generator,
            AliasGenerator::class,
            sprintf('Service %s is not an alias generator.', $serviceId)
        );
    }
}
