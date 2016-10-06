<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias\Validator;

use Database;
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
     * @var Database
     */
    private $database;

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
     * Unique query.
     *
     * @var string
     */
    private $query;

    /**
     * Allow an empty alias.
     *
     * @var bool
     */
    private $allowEmptyAlias;

    /**
     * UniqueDatabaseValueValidator constructor.
     *
     * @param Database $database        Database connection.
     * @param string   $tableName       Table name.
     * @param string   $columnName      Column name.
     * @param bool     $allowEmptyAlias Allow empty alias.
     */
    public function __construct(Database $database, $tableName, $columnName, $allowEmptyAlias = false)
    {
        $this->database        = $database;
        $this->tableName       = $tableName;
        $this->columnName      = $columnName;
        $this->allowEmptyAlias = $allowEmptyAlias;

        $this->query = sprintf(
            'SELECT count(*) AS result FROM %s WHERE %s=?',
            $this->tableName,
            $this->columnName
        );
    }

    /**
     * {@inheritDoc}
     */
    public function validate($result, $value, array $exclude = null)
    {
        if (!$this->allowEmptyAlias && $value === '') {
            return false;
        }

        $query = $this->query;

        if ($exclude) {
            $query .= ' AND id NOT IN(?' . str_repeat(',?', (count($exclude) - 1)) . ')';
            $value  = array_merge([$value], $exclude);
        }

        $result = $this->database
            ->prepare($query)
            ->execute($value);

        return $result->result == 0;
    }
}
