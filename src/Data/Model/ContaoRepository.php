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
 * Class ContaoRepository.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Model
 */
class ContaoRepository implements Repository
{
    /**
     * The model class.
     *
     * @var string
     */
    private $modelClass;

    /**
     * ContaoRepository constructor.
     *
     * @param string $modelClass Model class.
     */
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * Get the table name.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->call('getTable');
    }

    /**
     * {@inheritDoc}
     */
    public function find($modelId)
    {
        return $this->call('findByPK', [$modelId]);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $column, array $values, array $options = [])
    {
        return $this->call('findBy', [$column, $values, $options]);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $column, array $values, array $options = [])
    {
        return $this->call('findOneBy', [$column, $values, $options]);
    }

    /**
     * {@inheritDoc}
     */
    public function findBySpecification(Specification $specification, array $options = [])
    {
        $column = [];
        $values = [];

        $specification->buildQuery($column, $values);

        return $this->findBy($column, $values, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(array $options = [])
    {
        return $this->call('findAll', [$options]);
    }

    /**
     * {@inheritDoc}
     */
    public function countBy(array $column, array $values)
    {
        return $this->call('countBy', [$column, $values]);
    }

    /**
     * {@inheritDoc}
     */
    public function countBySpecification(Specification $specification)
    {
        $column = [];
        $values = [];

        $specification->buildQuery($column, $values);

        return $this->countBy($column, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function countAll()
    {
        return $this->call('countAll');
    }

    /**
     * {@inheritDoc}
     */
    public function save(Model $model)
    {
        $model->save();
    }

    /**
     * Make a call on the model.
     *
     * @param string $method    Method name.
     * @param array  $arguments Arguments.
     *
     * @return mixed
     */
    protected function call($method, $arguments = [])
    {
        return call_user_func_array([$this->modelClass, $method], $arguments);
    }
}
