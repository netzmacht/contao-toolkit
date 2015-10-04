<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database;
use Contao\Database\Result;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * Alias generator.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias
 */
class Generator
{
    /**
     * The database connection.
     *
     * @var \Database
     */
    private $database;

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
     * Filters being applied when standardize the value.
     *
     * @var Filter[]
     */
    private $filters = array();

    /**
     * Value separator.
     *
     * @var string
     */
    private $separator;

    /**
     * Unique query.
     *
     * @var string
     */
    private $query;

    /**
     * Construct.
     *
     * @param Filter[] $filters    Filters.
     * @param Database $database   The database connection.
     * @param string   $tableName  The table name.
     * @param string   $aliasField The alias field.
     * @param string   $separator  Value separator.
     */
    public function __construct($filters, Database $database, $tableName, $aliasField = 'alias', $separator = '-')
    {
        $this->database   = $database;
        $this->aliasField = $aliasField;
        $this->tableName  = $tableName;
        $this->separator  = $separator;
        $this->filters    = $filters;

        $this->query = sprintf(
            'SELECT count(*) AS result FROM %s WHERE %s=? AND id !=?',
            $this->tableName,
            $this->aliasField
        );
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
     * Get separator.
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * Get all filters.
     *
     * @return Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
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
        $result = $this->database->prepare($this->query)->execute($value, $rowId);

        return $result->result == 0;
    }

    /**
     * Apply filters.
     *
     * @param Database|Model $result Data record.
     * @param mixed          $value  Given value.
     *
     * @return mixed
     */
    private function applyFilters($result, $value)
    {
        foreach ($this->filters as $filter) {
            $filter->initialize();

            do {
                $value  = $filter->apply($result, $value, $this->separator);
                $unique = $this->isUniqueValue($value, $result->id);

                if ($filter->breakIfUnique() && $unique) {
                    break 2;
                }
            } while ($filter->repeatUntilUnique() && !$unique);
        }

        return $value;
    }

    /**
     * Guard that a valid alias is given.
     *
     * @param Database|Model $result Data record.
     * @param mixed          $value  Given value.
     *
     * @return void
     * @throws \RuntimeException When No unique alias is generated.
     */
    private function guardValidAlias($result, $value)
    {
        if (!$value || !$this->isUniqueValue($value, $result->id)) {
            throw new \RuntimeException(
                sprintf(
                    'Could not create unique alias for "%s::%s". Alias value "%s"',
                    $this->tableName,
                    $result->id,
                    $value
                )
            );
        }
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
        $value = $this->applyFilters($result, $value);
        $this->guardValidAlias($result, $value);

        return $value;
    }
}
