<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Validator;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator;
use Override;

/**
 * Class UniqueDatabaseValueValidator validates a value as true if it does not exists in the database.
 */
final class UniqueDatabaseValueValidator implements Validator
{
    /**
     * Database connection.
     */
    private Connection $connection;

    /**
     * Table name.
     */
    private string $tableName;

    /**
     * Column name.
     */
    private string $columnName;

    /**
     * Allow an empty alias.
     */
    private bool $allowEmptyAlias;

    /**
     * Data fields being used as unique fields.
     *
     * @var list<string>
     */
    private array $uniqueKeyFields;

    /**
     * @param Connection   $connection      Database connection.
     * @param string       $tableName       Table name.
     * @param string       $columnName      Alias value column name.
     * @param list<string> $uniqueKeyFields Other fields which spans the unique key along the alias column.
     * @param bool         $allowEmptyAlias Allow empty alias.
     */
    public function __construct(
        Connection $connection,
        string $tableName,
        string $columnName,
        array $uniqueKeyFields = [],
        bool $allowEmptyAlias = false,
    ) {
        $this->connection      = $connection;
        $this->tableName       = $tableName;
        $this->columnName      = $columnName;
        $this->allowEmptyAlias = $allowEmptyAlias;
        $this->uniqueKeyFields = $uniqueKeyFields;
    }

    /** {@inheritDoc} */
    #[Override]
    public function validate(object $result, mixed $value, array|null $exclude = null): bool
    {
        if (! $this->allowEmptyAlias && empty($value)) {
            return false;
        }

        $builder = $this->connection->createQueryBuilder();
        $builder
            ->select('count(*) AS result')
            ->from($this->tableName)
            ->where($this->columnName . '= :value')
            ->setParameter('value', $value);

        foreach ($this->uniqueKeyFields as $field) {
            $builder->andWhere($field . '= :' . $field);
            $builder->setParameter($field, $result->$field);
        }

        if ($exclude !== null) {
            $builder->andWhere('id NOT IN(:excluded)');
            $builder->setParameter('excluded', $exclude, ArrayParameterType::INTEGER);
        }

        return (int) $builder->fetchOne() === 0;
    }
}
