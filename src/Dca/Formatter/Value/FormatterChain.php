<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Assertion\Assertion;
use Override;

use function array_map;
use function is_array;

/**
 * Set of multiple formatter.
 */
final class FormatterChain implements ValueFormatter
{
    /**
     * List of value formatter.
     *
     * @var ValueFormatter[]
     */
    private array $formatter;

    /** @param ValueFormatter[]|array $formatter Value formatter. */
    public function __construct(array $formatter)
    {
        Assertion::allImplementsInterface($formatter, ValueFormatter::class);

        $this->formatter = $formatter;
    }

    /** {@inheritDoc} */
    #[Override]
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        foreach ($this->formatter as $formatter) {
            if ($formatter->accepts($fieldName, $fieldDefinition)) {
                return true;
            }
        }

        return false;
    }

    /** {@inheritDoc} */
    #[Override]
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        foreach ($this->formatter as $formatter) {
            if (! $formatter->accepts($fieldName, $fieldDefinition)) {
                continue;
            }

            // Multiple values are formatted one by one, they get flattened by the post filters.
            if (is_array($value)) {
                return array_map(
                    static fn (mixed $item): mixed => $formatter->format($item, $fieldName, $fieldDefinition, $context),
                    $value,
                );
            }

            return $formatter->format($value, $fieldName, $fieldDefinition, $context);
        }

        return $value;
    }
}
