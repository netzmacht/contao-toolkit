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
     * Find records by various criteria.
     *
     * @param array $column  Column criteria.
     * @param array $values  Column values.
     * @param array $options Options.
     *
     * @return \Model\Collection|null
     */
    public function findBy(array $column, array $values, array $options = []);

    /**
     * Find single record by various criteria.
     *
     * @param array $column  Column criteria.
     * @param array $values  Column values.
     * @param array $options Options.
     *
     * @return \Model|null
     */
    public function findOneBy(array $column, array $values, array $options = []);

    /**
     * Find records by specification.
     *
     * @param Specification $specification Specification.
     * @param array         $options       Options.
     *
     * @return \Model\Collection|\Model|null
     */
    public function findBySpecification(Specification $specification, array $options = []);

    /**
     * Find all records.
     *
     * @param array $options Query options.
     *
     * @return \Model\Collection|\Model|null
     */
    public function findAll(array $options = []);

    /**
     * Count by various criteria.
     *
     * @param array $column Column criteria.
     * @param array $values Column values.
     *
     * @return int
     */
    public function countBy(array $column, array $values);

    /**
     * Count by specification.
     *
     * @param Specification $specification Specification.
     *
     * @return int
     */
    public function countBySpecification(Specification $specification);

    /**
     * The total number of rows.
     *
     * @return int
     */
    public function countAll();

    /**
     * Save a model.
     *
     * @param Model $model Model being saved.
     *
     * @return void
     */
    public function save(Model $model);
}
