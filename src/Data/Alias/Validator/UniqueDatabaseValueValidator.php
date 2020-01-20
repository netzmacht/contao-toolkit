<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Validator;

use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator;

/**
 * Class UniqueDatabaseValueValidator validates a value as true if it does not exists in the database.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Validator
 */
final class UniqueDatabaseValueValidator implements Validator
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Table name.
     *
     * @var string
     */
    private $tableName;

    /**
     * Column name.
     *
     * @var string
     */
    private $columnName;

    /**
     * Allow an empty alias.
     *
     * @var bool
     */
    private $allowEmptyAlias;

    /**
     * Data fields being used as unique fields.
     *
     * @var array
     */
    private $uniqueKeyFields;

    /**
     * UniqueDatabaseValueValidator constructor.
     *
     * @param Connection $connection      Database connection.
     * @param string     $tableName       Table name.
     * @param string     $columnName      Alias value column name.
     * @param array      $uniqueKeyFields Other fields which spans the unique key along the alias column.
     * @param bool       $allowEmptyAlias Allow empty alias.
     */
    public function __construct(
        Connection $connection,
        string $tableName,
        string $columnName,
        array $uniqueKeyFields = [],
        bool $allowEmptyAlias = false
    ) {
        $this->connection      = $connection;
        $this->tableName       = $tableName;
        $this->columnName      = $columnName;
        $this->allowEmptyAlias = $allowEmptyAlias;
        $this->uniqueKeyFields = $uniqueKeyFields;
    }

    /**
     * {@inheritDoc}
     */
    public function validate($result, $value, array $exclude = null): bool
    {
        if (!$this->allowEmptyAlias && $value == '') {
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

        if ($exclude) {
            $builder->andWhere('id NOT IN(:excluded)');
            $builder->setParameter('excluded', $exclude, Connection::PARAM_INT_ARRAY);
        }

        $statement = $builder->execute();

        return $statement->fetch()['result'] == 0;
    }
}
