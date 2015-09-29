<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data;

use Database\Result;
use \Model;

/**
 * Class AliasGenerator creates an alias and can handle different strategies.
 *
 * @package Netzmacht\Contao\Toolkit\Data
 */
class AliasGenerator
{
    const STRATEGY_ALL    = 1;
    const STRATEGY_FORCE  = 2;
    const STRATEGY_ADD_ID = 4;

    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

    /**
     * The columns being combined for the alias value.
     *
     * @var array
     */
    private $columns;

    /**
     * Alias generation strategies.
     *
     * @var int
     */
    private $strategy = self::STRATEGY_ADD_ID;

    /**
     * The alias field.
     *
     * @var string
     */
    private $aliasField;

    /**
     * The table name.
     *
     * @var string
     */
    private $tableName;

    /**
     * The suffix index limit.
     *
     * If limit is 0, no limit is given.
     *
     * @var int
     */
    private $limit = 0;

    /**
     * Filters being applied when standardize the value.
     *
     * @var array
     */
    private $filters = array();

    /**
     * Construct.
     *
     * @param \Database $database   The database connection.
     * @param string    $tableName  The table name.
     * @param string    $aliasField The alias field.
     * @param array     $columns    Columns being included into the alias.
     */
    public function __construct(\Database $database, $tableName, $aliasField = 'alias', array $columns = array())
    {
        $this->database   = $database;
        $this->columns    = $columns;
        $this->aliasField = $aliasField;
        $this->tableName  = $tableName;
        $this->filters[]  = 'standardize';
    }

    /**
     * Add a column.
     *
     * @param string $column The column name.
     *
     * @return $this
     */
    public function addColumn($column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Add multiple columns.
     *
     * @param array $columns The column names.
     *
     * @return $this
     */
    public function addColumns(array $columns)
    {
        $this->columns = array_merge($this->columns, $columns);

        return $this;
    }

    /**
     * Set the alias field.
     *
     * @param string $field The alias field.
     *
     * @return $this
     */
    public function setAliasField($field)
    {
        $this->aliasField = $field;

        return $this;
    }

    /**
     * Get the alias field.
     *
     * @return string
     */
    public function getAliasField()
    {
        return $this->aliasField;
    }

    /**
     * The table name.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set table name.
     *
     * @param string $tableName The table name.
     *
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get used strategy.
     *
     * @return int
     */
    public function getStrategy()
    {
        return $this->strategy;
    }

    /**
     * The the strategy.
     *
     * @param int $strategy The strategy.
     *
     * @return $this
     */
    public function setStrategy($strategy)
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * Get the limit.
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the limit.
     *
     * @param int $limit The limit.
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Add standardize filter.
     *
     * @param callable $filter Filter callback.
     *
     * @return $this
     */
    public function addFilter($filter)
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Generate the alias.
     *
     * @param Result|Model $result The database result.
     * @param mixed        $value  The current value.
     *
     * @return mixed|null|string
     */
    public function generate($result, $value = null)
    {
        $value = ($value === null) ? $result->{$this->aliasField} : $value;
        $value = $this->standardize($value);

        // Check if already an valid alias exists.
        if ($this->isAlreadyUnique($result, $value) || $this->createDefaultAlias($value, $result)) {
            return $value;
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

        return $this->suffixAlias($result->id, $value);
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
        $query = sprintf(
            'SELECT count(*) AS result FROM %s WHERE %s=? AND id !=?',
            $this->tableName,
            $this->aliasField
        );

        $result = $this->database->prepare($query)->execute($value, $rowId);

        return $result->result == 0;
    }

    /**
     * Check if a strategy is given.
     *
     * @param int $strategy The strategy.
     *
     * @return bool
     */
    private function hasStrategy($strategy)
    {
        return ($this->strategy & $strategy) === $strategy;
    }

    /**
     * Standardize the value.
     *
     * @param mixed $value The value.
     *
     * @return string
     */
    private function standardize($value)
    {
        foreach ($this->filters as $filter) {
            $value = call_user_func($filter, $value);
        }

        return $value;
    }

    /**
     * Create default alias without suffix by combining the fields.
     *
     * It returns true, if the value is unique for sure.
     *
     * @param mixed  $value  Current value.
     * @param Result $result The database result.
     *
     * @return bool
     */
    private function createDefaultAlias(&$value, $result)
    {
        $value = '';

        foreach ($this->columns as $column) {
            if ($value) {
                $value .= '_';
            }

            $value .= $this->standardize($result->$column);

            if (!$this->hasStrategy(static::STRATEGY_ALL)) {
                if ($this->isUniqueValue($value, $result->id)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Add suffixes to alias until it's unique or limit is reached.
     *
     * @param int    $rowId The current record id.
     * @param string $value The current alias.
     *
     * @return string
     *
     * @throws \RuntimeException If the alias could not be generated.
     */
    private function suffixAlias($rowId, $value)
    {
        $suffix = '';
        $index  = 2;

        while (!$this->isUniqueValue($value . $suffix, $rowId)) {
            $suffix = '_' . ($index++);

            if ($this->limit > 0 && $this->limit <= $index) {
                throw new \RuntimeException(
                    sprintf('Alias can not be generated. Suffix limit of "%s" reached', $this->limit)
                );
            }
        }

        return $value . $suffix;
    }

    /**
     * Check if alias is already unique.
     *
     * @param Result $result The database result.
     * @param string $value  The given alias value.
     *
     * @return bool
     */
    private function isAlreadyUnique($result, $value)
    {
        return !$this->hasStrategy(static::STRATEGY_FORCE) && $value && $this->isUniqueValue($value, $result->id);
    }
}
