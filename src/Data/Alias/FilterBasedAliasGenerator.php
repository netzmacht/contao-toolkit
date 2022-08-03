<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database\Result;
use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException;

/**
 * Alias generator.
 */
final class FilterBasedAliasGenerator implements AliasGenerator
{
    /**
     * Alias validator.
     */
    private Validator $validator;

    /**
     * The alias field.
     */
    private string $aliasField;

    /**
     * The table name.
     */
    private string $tableName;

    /**
     * Filters being applied when standardize the value.
     *
     * @var Filter[]
     */
    private iterable $filters;

    /**
     * Value separator.
     */
    private string $separator;

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
        array $filters,
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
     */
    public function getAliasField(): string
    {
        return $this->aliasField;
    }

    /**
     * The table name.
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Get separator.
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
     * @param Result|Model $result Data record.
     * @param mixed        $value  The alias value.
     * @param int          $rowId  The row id.
     */
    private function isValid($result, $value, int $rowId): bool
    {
        return $this->validator->validate($result, $value, [$rowId]);
    }

    /**
     * Apply filters.
     *
     * @param Result|Model $result Data record.
     * @param mixed        $value  Given value.
     *
     * @return mixed
     */
    private function applyFilters($result, $value)
    {
        foreach ($this->filters as $filter) {
            $filter->initialize();

            do {
                $value = $filter->apply($result, $value, $this->separator);
                /** @psalm-suppress RedundantCastGivenDocblockType */
                $unique = $this->isValid($result, $value, (int) $result->id);

                if ($filter->breakIfValid() && $unique) {
                    break 2;
                }
            } while ($filter->repeatUntilValid() && ! $unique);
        }

        return $value;
    }

    /**
     * Guard that a valid alias is given.
     *
     * @param Result|Model $result Data record.
     * @param mixed        $value  Given value.
     *
     * @throws InvalidAliasException When No unique alias is generated.
     */
    private function guardValidAlias($result, $value): void
    {
        /** @psalm-suppress RedundantCastGivenDocblockType */
        if (! $value || ! $this->isValid($result, $value, (int) $result->id)) {
            /** @psalm-suppress RedundantCastGivenDocblockType */
            throw InvalidAliasException::forDatabaseEntry($this->tableName, (int) $result->id, $value);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function generate($result, $value = null): string
    {
        $value = $this->applyFilters($result, $value);
        $this->guardValidAlias($result, $value);

        return $value;
    }
}
