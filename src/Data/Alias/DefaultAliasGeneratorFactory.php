<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;

/**
 * Class DefaultAliasGeneratorFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias
 */
class DefaultAliasGeneratorFactory
{
    /**
     * Database connection.
     *
     * @var Database
     */
    private $connection;

    /**
     * DefaultAliasGeneratorFactory constructor.
     *
     * @param Database $connection Database connection.
     */
    public function __construct(Database $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Invoke will create the alias generator for the given data container and the alias fields.
     *
     * @param string  $dataContainerName Data container name.
     * @param string  $aliasField        Alias field name.
     * @param array   $fields            List of value fields.
     *
     * @return FilterBasedAliasGenerator
     */
    public function __invoke($dataContainerName, $aliasField, array $fields)
    {
        $filters = [
            new ExistingAliasFilter(),
            new SlugifyFilter($fields),
            new SuffixFilter()
        ];

        $validator = new UniqueDatabaseValueValidator(
            $this->connection,
            $dataContainerName,
            $aliasField
        );

        return new FilterBasedAliasGenerator($filters, $validator, $dataContainerName, $aliasField);
    }
}
