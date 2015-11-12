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
