<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use ArrayAccess;
use Contao\Database\Result;
use Contao\Model;

/**
 * ModelArrayAccess decorates a Contao data model to provide array access.
 */
final class ModelArrayAccess implements ArrayAccess
{
    /**
     * Data model.
     *
     * @var Model|Result
     */
    private $model;

    /**
     * @param Result|Model $model Data model as database result of model.
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->model->$offset);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->model->$offset;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value): void
    {
        $this->model->$offset = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset): void
    {
        $this->model->$offset = null;
    }
}
