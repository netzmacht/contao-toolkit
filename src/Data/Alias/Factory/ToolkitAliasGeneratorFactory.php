<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Factory;

use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\FilterBasedAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;

final class ToolkitAliasGeneratorFactory implements AliasGeneratorFactory
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection Database connection.
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $dataContainerName, string $aliasField, array $fields): AliasGenerator
    {
        $filters = [
            new ExistingAliasFilter(),
            new SlugifyFilter($fields),
            new SuffixFilter(),
        ];

        $validator = new UniqueDatabaseValueValidator(
            $this->connection,
            $dataContainerName,
            $aliasField
        );

        return new FilterBasedAliasGenerator($filters, $validator, $dataContainerName, $aliasField);
    }
}
