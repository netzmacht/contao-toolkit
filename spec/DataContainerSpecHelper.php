<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit;

use Contao\DataContainer;
use ReflectionClass;
use ReflectionProperty;

trait DataContainerSpecHelper
{
    private function createDataContainer(
        string $table,
        string $field = '',
        mixed $value = null,
        object|null $activeRecord = null,
    ): DataContainer {
        $dataContainer = (new ReflectionClass(ConcreteDataContainer::class))->newInstanceWithoutConstructor();

        $this->setDataContainerProperty($dataContainer, 'strTable', $table);
        $this->setDataContainerProperty($dataContainer, 'strField', $field);
        $this->setDataContainerProperty($dataContainer, 'varValue', $value);
        $this->setDataContainerProperty($dataContainer, 'objActiveRecord', $activeRecord);

        return $dataContainer;
    }

    private function setDataContainerProperty(DataContainer $dataContainer, string $property, mixed $value): void
    {
        $reflectionProperty = new ReflectionProperty(DataContainer::class, $property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($dataContainer, $value);
    }
}
