<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\DevTools\Data;

use Database\Result;

class AliasGenerator
{
    const STRATEGY_ALL    = 1;
    const STRATEGY_FORCE  = 2;
    const STRATEGY_ADD_ID = 4;

    private $database;

    private $columns;

    private $strategy = self::STRATEGY_ADD_ID;

    /**
     * @var string
     */
    private $aliasField;

    private $tableName;

    private $limit = 0;

    /**
     * @param \Database $database
     * @param string    $aliasField
     * @param array     $columns
     */
    public function __construct(\Database $database, $tableName, $aliasField = 'alias', array $columns = array())
    {
        $this->database   = $database;
        $this->columns    = $columns;
        $this->aliasField = $aliasField;
        $this->tableName  = $tableName;
    }

    public function addColumn($column)
    {
        $this->columns[] = $column;

        return $this;
    }

    public function addColumns(array $columns)
    {
        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    public function setAliasField($field)
    {
        $this->aliasField = $field;

        return $this;
    }

    public function getAliasField()
    {
        return $this->aliasField;
    }

    /**
     * @return mixed
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set table name.
     *
     * @param string $tableName
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * @return int
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * @param int $strategy
     *
     * @return $this
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param Result $result
     * @param null   $value
     *
     * @return mixed|null|string
     */
    public function generate(Result $result, $value = null)
    {
        $value = ($value === null) ? $result->{$this->aliasField} : $value;
        $value = $this->standardize($value);

        // Check if already an valid alias exists.
        if (!$this->hasStrategy(static::STRATEGY_FORCE) && $value && $this->isUniqueValue($value, $result->id)) {
            return $value;
        }

        $value = '';

        foreach ($this->columns as $column) {
            if ($value) {
                $value .= '_';
            }

            $value .= $this->standardize($result->$column);

            if (!$this->hasStrategy(static::STRATEGY_ALL)) {
                if ($this->isUniqueValue($value, $result->id)) {
                    return $value;
                }
            }
        }

        if ($this->isUniqueValue($value, $result->id)) {
            // Value is already unique, just return.
            return $value;

        } elseif ($this->hasStrategy(static::STRATEGY_ADD_ID)) {
            // Try to add the id first.

            $value .= '_' . $result->id;
            if ($this->isUniqueValue($value, $result->id)) {
                return $value;
            }
        }

        $suffix = '';
        $index  = 2;

        while (!$this->isUniqueValue($value . $suffix, $result->id)) {
            $suffix = '_' . $index++;

            if ($this->limit > 0 && $this->limit <= $index) {
                throw new \InvalidArgumentException(
                    sprintf('Alias can not be generated. Suffix limit of "%s" reached', $this->limit)
                );
            }
        }

        return $value . $suffix;
    }

    /**
     * Consider if value is an unique value.
     *
     * @param mixed $value The alias value.
     * @param int   $rowId The row id.
     *
     * @return bool
     */
    private function isUniqueValue($value, $rowId)
    {
        $query= sprintf(
            'SELECT count(*) AS result FROM %s WHERE %s=? AND id !=?',
            $this->tableName,
            $this->aliasField
        );

        $result = $this->database->prepare($query)->execute($value, $rowId);

        return $result->result == 0;
    }

    /**
     * @param $strategy
     *
     * @return bool
     */
    private function hasStrategy($strategy)
    {
        return ($this->strategy & $strategy) === $strategy;
    }

    /**
     * Standardize the value
     *
     * @param mixed $value The value.
     *
     * @return string
     */
    protected function standardize($value)
    {
        return str_replace('-', '_', standardize($value));
    }
}
