<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException;

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
     * @var array|Filter[]
     */
    private $filters;

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
    public function __construct(
        $filters,
        Validator $validator,
        string $tableName,
        string $aliasField = 'alias',
        string $separator = '-'
    ) {
        Assertion::allIsInstanceOf($filters, Filter::class);

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
    public function getAliasField(): string
    {
        return $this->aliasField;
    }

    /**
     * The table name.
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Get separator.
     *
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * Get all filters.
     *
     * @return array|Filter[]
     */
    public function getFilters(): array
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
    private function isValid($result, $value, int $rowId): bool
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
                $unique = $this->isValid($result, $value, (int) $result->id);

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
    private function guardValidAlias($result, $value): void
    {
        if (!$value || !$this->isValid($result, $value, (int) $result->id)) {
            throw InvalidAliasException::forDatabaseEntry($this->tableName, (int) $result->id, $value);
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
