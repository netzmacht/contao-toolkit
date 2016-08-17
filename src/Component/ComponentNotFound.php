<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

use Database\Result;

/**
 * Exception class ContentElementNotFound is thrown if a content element could not be created.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
class ComponentNotFound extends \Exception
{
    /**
     * Create exception for the given model.
     *
     * @param \Model|Result $model Component model.
     *
     * @return static
     */
    public static function forModel($model)
    {
        $message = sprintf(
            '%s of type "%s" not found for model ID %s',
            static::describeModel($model),
            $model->type,
            $model->id
        );

        return new static($message);
    }

    /**
     * Describe the model.
     *
     * @param \Model $model Component model.
     *
     * @return string
     */
    private static function describeModel($model)
    {
        if ($model instanceof \ContentModel) {
            return 'Content element';
        }

        if ($model instanceof \ModuleModel) {
            return 'Frontend module';
        }

        return 'Component';
    }
}
