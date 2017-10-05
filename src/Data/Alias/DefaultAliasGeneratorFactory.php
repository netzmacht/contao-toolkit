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

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;

/**
 * Class DefaultAliasGeneratorFactory.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias
 */
final class DefaultAliasGeneratorFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * DefaultAliasGeneratorFactory constructor.
     *
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Invoke will create the alias generator for the given data container and the alias fields.
     *
     * @param string $dataContainerName Data container name.
     * @param string $aliasField        Alias field name.
     * @param array  $fields            List of value fields.
     *
     * @return FilterBasedAliasGenerator
     */
    public function __invoke(string $dataContainerName, string $aliasField, array $fields)
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
