<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data;

use Contao\Database\Result;
use Contao\Model;

/**
 * ModelArrayAccess decorates a Contao data model to provide array access.
 *
 * @package Netzmacht\Contao\Toolkit\Data
 */
class ModelArrayAccess implements \ArrayAccess
{
    /**
     * Data model.
     *
     * @var Model|Result
     */
    private $model;

    /**
     * ModelArrayAccess constructor.
     *
     * @param Result|Model $model Data model as database result of model.
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->model->$offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->model->$offset;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->model->$offset = $value;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->model->$offset = null;
    }
}
