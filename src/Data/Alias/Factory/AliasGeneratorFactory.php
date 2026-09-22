<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Factory;

use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;

/**
 * @deprecated Implement Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator directly instead.
 *             Will be removed in 5.0.
 */
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
