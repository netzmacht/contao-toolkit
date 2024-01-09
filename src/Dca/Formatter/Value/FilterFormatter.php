<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Assertion\Assertion;

/**
 * FilterFormatter applies formatter used as filters to an value.
 *
 * The difference between the FormatterChain and the FilterFormatter is that the filter formatter will apply all
 * rules and modifies the given value.
 */
final class FilterFormatter implements ValueFormatter
{
    /**
     * List of filters.
     *
     * @var ValueFormatter[]
     */
    private array $filters;

    /**
     * Construct.
     *
     * @param ValueFormatter[]|array $filters List of filters.
     */
    public function __construct(array $filters)
    {
        Assertion::allImplementsInterface($filters, ValueFormatter::class);

        $this->filters = $filters;
    }

    /** {@inheritDoc} */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return true;
    }

    /** {@inheritDoc} */
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        foreach ($this->filters as $filter) {
            if (! $filter->accepts($fieldName, $fieldDefinition)) {
                continue;
            }

            $value = $filter->format($value, $fieldName, $fieldDefinition, $context);
        }

        return $value;
    }
}
