<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;

/**
 * Interface Repository describes a very base repository.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Model
 */
interface Repository
{
    /**
     * Find a model id it's id. Returns null if not found.
     *
     * @param int $modelId Model id.
     *
     * @return Model|null
     */
    public function find($modelId);

    /**
     * Save a model.
     *
     * @param Model $model Model being saved.
     *
     * @return void
     */
    public function save(Model $model);
}
