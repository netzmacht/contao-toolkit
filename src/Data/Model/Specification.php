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
     *
     * @return bool
     *
     * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function isSatisfiedBy(Model $model);

    /**
     * Transform the specification into an model query.
     *
     * @param list<string> $columns Columns array.
     * @param list<mixed>  $values  Values array.
     *
     * @return void
     *
     * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function buildQuery(array &$columns, array &$values);
}
