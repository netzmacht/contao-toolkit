<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;

/**
 * Specification defines search criteria and translate them into queries.
 */
interface Specification
{
    /**
     * Consider if the specification is satisfied by the given model.
     *
     * @param Model $model Given model.
     */
    public function isSatisfiedBy(Model $model): bool;

    /**
     * Transform the specification into a model query.
     *
     * @param list<string> $columns Columns array.
     * @param list<mixed>  $values  Values array.
     */
    public function buildQuery(array &$columns, array &$values): void;
}
