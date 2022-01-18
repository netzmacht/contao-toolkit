<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Factory;

use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;

interface AliasGeneratorFactory
{
    /**
     * Create an alias generator.
     *
     * @param string       $dataContainerName Data container name.
     * @param string       $aliasField        Alias field.
     * @param list<string> $fields            List of fields for the alias value.
     */
    public function create(string $dataContainerName, string $aliasField, array $fields): AliasGenerator;
}
