<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Save;

use Assert\AssertionFailedException;
use Contao\Database\Result;
use Contao\DataContainer;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Factory\AliasGeneratorFactory;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use function assert;
use function sprintf;

/**
 * Class GenerateAliasCallback is designed to create an alias of a column.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
final class GenerateAliasListener
{
    /**
     * Dependency container.
     */
    private Container $container;

    /**
     * Data container manager.
     */
    private DcaManager $dcaManager;

    /**
     * Default alias generator factory service id.
     */
    private string $defaultFactoryServiceId;

    /**
     * Cache of created alias generators.
     *
     * @var AliasGenerator[][]
     */
    private array $generators = [];

    /**
     * Construct.
     *
     * @param Container  $container               Dependency container.
     * @param DcaManager $dcaManager              Data container manager.
     * @param string     $defaultFactoryServiceId Default alias generator factory service id.
     */
    public function __construct(Container $container, DcaManager $dcaManager, string $defaultFactoryServiceId)
    {
        $this->container               = $container;
        $this->defaultFactoryServiceId = $defaultFactoryServiceId;
        $this->dcaManager              = $dcaManager;
    }

    /**
     * Generate the alias value.
     *
     * @param mixed         $value         The current value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @throws AssertionFailedException If invalid data container is given.
     */
    public function onSaveCallback($value, DataContainer $dataContainer): string|null
    {
        Assertion::true(
            $dataContainer->activeRecord instanceof Result || $dataContainer->activeRecord instanceof Model,
        );

        return $this->getGenerator($dataContainer)->generate($dataContainer->activeRecord, $value);
    }

    /**
     * Get the service id.
     *
     * @param DataContainer $dataContainer Data container.
     */
    private function getFactoryServiceId($dataContainer): string
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);

        return $definition->get(
            ['fields', $dataContainer->field, 'toolkit', 'alias_generator', 'factory'],
            $this->defaultFactoryServiceId,
        );
    }

    /**
     * Guard that service is an alias generator.
     *
     * @param mixed  $factory   Retrieved alias generator factory service.
     * @param string $serviceId Service id.
     */
    private function guardIsAliasGeneratorFactory($factory, string $serviceId): void
    {
        Assertion::isInstanceOf(
            $factory,
            AliasGeneratorFactory::class,
            sprintf('Service %s is not an alias generator factory.', $serviceId),
        );
    }

    /**
     * Generate an alias generator.
     *
     * @param DataContainer $dataContainer Data container driver.
     */
    private function getGenerator($dataContainer): AliasGenerator
    {
        if (isset($this->generators[$dataContainer->table][$dataContainer->field])) {
            return $this->generators[$dataContainer->table][$dataContainer->field];
        }

        $serviceId = $this->getFactoryServiceId($dataContainer);
        $factory   = $this->container->get($serviceId);
        assert($factory instanceof AliasGeneratorFactory);
        $definition = $this->dcaManager->getDefinition($dataContainer->table);
        $fields     = (array) $definition->get(
            ['fields', $dataContainer->field, 'toolkit', 'alias_generator', 'fields'],
            ['id'],
        );

        $this->guardIsAliasGeneratorFactory($factory, $serviceId);

        return $factory->create($dataContainer->table, $dataContainer->field, $fields);
    }
}
