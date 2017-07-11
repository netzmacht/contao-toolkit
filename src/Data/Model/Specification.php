<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;

/**
 * Specification defines search criteria and translate them into queries.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Model
 */
interface Specification
{
    /**
     * Consider if the specification is satisfied by the given model.
     *
     * @param Model $model Given model.
     *
     * @return bool
     */
    public function isSatisfiedBy(Model $model);

    /**
     * Transform the specification into an model query.
     *
     * @param array $columns Columns array.
     * @param array $values  Values array.
     *
     * @return void
     */
    public function buildQuery(array &$columns, array &$values);
}
