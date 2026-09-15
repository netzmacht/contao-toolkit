<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\CoreBundle\Slug\Slug;
use Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException;
use Override;

use function array_filter;
use function implode;

final class SlugAliasGenerator implements AliasGenerator
{
    /** @param list<string> $fields */
    public function __construct(
        private readonly Slug $slug,
        private readonly Validator $validator,
        private readonly string $tableName,
        private readonly array $fields = ['id'],
        private readonly string $separator = '-',
    ) {
    }

    #[Override]
    public function generate(object $result, mixed $value = null): string
    {
        $value = $value !== null ? (string) $value : '';

        if ($value !== '') {
            $this->guardValidAlias($result, $value);

            return $value;
        }

        $generated = $this->slug->generate(
            $this->buildSourceText($result),
            ['delimiter' => $this->separator],
            fn (string $alias): bool => ! $this->validator->validate($result, $alias, [(int) $result->id]),
        );

        $this->guardValidAlias($result, $generated);

        return $generated;
    }

    private function buildSourceText(object $result): string
    {
        $values = [];

        foreach ($this->fields as $field) {
            $values[] = (string) $result->$field;
        }

        return implode(' ', array_filter($values, static fn (string $v): bool => $v !== ''));
    }

    private function guardValidAlias(object $result, string $value): void
    {
        if (! $this->validator->validate($result, $value, [(int) $result->id])) {
            throw InvalidAliasException::forDatabaseEntry($this->tableName, (int) $result->id, $value);
        }
    }
}
