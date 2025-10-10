<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException;
use Override;

/**
 * Alias generator.
 */
final class FilterBasedAliasGenerator implements AliasGenerator
{
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
        private readonly array $filters,
        private readonly Validator $validator,
        private readonly string $tableName,
        private readonly string $aliasField = 'alias',
        private readonly string $separator = '-',
    ) {
        Assertion::allIsInstanceOf($filters, Filter::class);
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
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Consider if value is an valid alias.
     *
     * @param object $result Data record.
     * @param mixed  $value  The alias value.
     * @param int    $rowId  The row id.
     */
    private function isValid(object $result, mixed $value, int $rowId): bool
    {
        return $this->validator->validate($result, $value, [$rowId]);
    }

    /**
     * Apply filters.
     *
     * @param object $result Data record.
     * @param mixed  $value  Given value.
     */
    private function applyFilters(object $result, mixed $value): mixed
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
     * @param object $result Data record.
     * @param mixed  $value  Given value.
     *
     * @throws InvalidAliasException When No unique alias is generated.
     */
    private function guardValidAlias(object $result, mixed $value): void
    {
        /** @psalm-suppress RedundantCastGivenDocblockType */
        if (! $value || ! $this->isValid($result, $value, (int) $result->id)) {
            /** @psalm-suppress RedundantCastGivenDocblockType */
            throw InvalidAliasException::forDatabaseEntry($this->tableName, (int) $result->id, $value);
        }
    }

    /** {@inheritDoc} */
    #[Override]
    public function generate(object $result, mixed $value = null): string
    {
        $value = $this->applyFilters($result, $value);
        $this->guardValidAlias($result, $value);

        return $value;
    }
}
