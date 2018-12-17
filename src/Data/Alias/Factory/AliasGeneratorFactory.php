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

namespace Netzmacht\Contao\Toolkit\Data\Alias\Factory;

use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;

/**
 * Interface AliasGeneratorFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias
 */
interface AliasGeneratorFactory
{
    /**
     * Create an alias generator.
     *
     * @param string $dataContainerName Data container name.
     * @param string $aliasField        Alias field.
     * @param array  $fields            List of fields for the alias value.
     *
     * @return AliasGenerator
     */
    public function create(string $dataContainerName, string $aliasField, array $fields): AliasGenerator;
}
