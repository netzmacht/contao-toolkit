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

namespace Netzmacht\Contao\Toolkit\Data\Alias\Validator;

use Contao\Database;
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
     * Data fields being used as unique fields.
     *
     * @var array
     */
    private $uniqueKeyFields;

    /**
     * UniqueDatabaseValueValidator constructor.
     *
     * @param Database $database        Database connection.
     * @param string   $tableName       Table name.
     * @param string   $columnName      Alias value column name.
     * @param array    $uniqueKeyFields Other fields which spans the unique key along the alias column.
     * @param bool     $allowEmptyAlias Allow empty alias.
     */
    public function __construct(
        Database $database,
        $tableName,
        $columnName,
        array $uniqueKeyFields = [],
        $allowEmptyAlias = false
    ) {
        $this->database        = $database;
        $this->tableName       = $tableName;
        $this->columnName      = $columnName;
        $this->allowEmptyAlias = $allowEmptyAlias;
        $this->uniqueKeyFields = $uniqueKeyFields;

        $this->query = sprintf(
            'SELECT count(*) AS result FROM %s WHERE %s=?',
            $this->tableName,
            $this->columnName
        );

        foreach ($uniqueKeyFields as $field) {
            $this->query .= sprintf(' AND %s=?', $field);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function validate($result, $value, array $exclude = null)
    {
        if (!$this->allowEmptyAlias && $value == '') {
            return false;
        }

        $query = $this->query;
        $value = [$value];

        foreach ($this->uniqueKeyFields as $field) {
            $value[] = $result->$field;
        }

        if ($exclude) {
            $query .= ' AND id NOT IN(?' . str_repeat(',?', (count($exclude) - 1)) . ')';
            $value  = array_merge($value, $exclude);
        }

        $result = $this->database
            ->prepare($query)
            ->execute($value);

        return $result->result == 0;
    }
}
