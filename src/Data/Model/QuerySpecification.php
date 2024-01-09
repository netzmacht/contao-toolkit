<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

interface QuerySpecification extends Specification
{
    /**
     * Transform the specification into a model query.
     *
     * @param list<string>        $columns Columns array.
     * @param list<mixed>         $values  Values array.
     * @param array<string,mixed> $options The query options.
     */
    public function buildQueryWithOptions(array &$columns, array &$values, array &$options): void;
}
