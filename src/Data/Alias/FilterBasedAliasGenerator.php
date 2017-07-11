<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Database;
use Model;
use Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException;
use Webmozart\Assert\Assert;

/**
 * Alias generator.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias
 */
final class FilterBasedAliasGenerator implements AliasGenerator
{
    /**
     * Alias validator.
     *
     * @var Validator
     */
    private $validator;

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
     * Construct.
     *
     * @param Filter[]  $filters    Filters.
     * @param Validator $validator  Alias validator.
     * @param string    $tableName  The table name.
     * @param string    $aliasField The alias field.
     * @param string    $separator  Value separator.
     */
    public function __construct($filters, Validator $validator, $tableName, $aliasField = 'alias', $separator = '-')
    {
        Assert::allIsInstanceOf($filters, Filter::class);

        $this->validator  = $validator;
        $this->aliasField = $aliasField;
        $this->tableName  = $tableName;
        $this->separator  = $separator;
        $this->filters    = $filters;
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
     * Consider if value is an valid alias.
     *
     * @param Database|Model $result Data record.
     * @param mixed          $value  The alias value.
     * @param int            $rowId  The row id.
     *
     * @return bool
     */
    private function isValid($result, $value, $rowId)
    {
        return $this->validator->validate($result, $value, [$rowId]);
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
                $unique = $this->isValid($result, $value, $result->id);

                if ($filter->breakIfValid() && $unique) {
                    break 2;
                }
            } while ($filter->repeatUntilValid() && !$unique);
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
     * @throws InvalidAliasException When No unique alias is generated.
     */
    private function guardValidAlias($result, $value)
    {
        if (!$value || !$this->isValid($result, $value, $result->id)) {
            throw InvalidAliasException::forDatabaseEntry($this->tableName, $result->id, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function generate($result, $value = null)
    {
        $value = $this->applyFilters($result, $value);
        $this->guardValidAlias($result, $value);

        return $value;
    }
}
