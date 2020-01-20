<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\Exception;

use Contao\ContentModel;
use Contao\Database\Result;
use Contao\Model;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Exception\Exception;
use RuntimeException;

/**
 * Exception class ContentElementNotFound is thrown if a content element could not be created.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
final class ComponentNotFound extends RuntimeException implements Exception
{
    /**
     * Create exception for the given model.
     *
     * @param Model|Result $model Component model.
     *
     * @return static
     */
    public static function forModel($model): self
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
     * @param Model $model Component model.
     *
     * @return string
     */
    private static function describeModel($model): string
    {
        if ($model instanceof ContentModel) {
            return 'Content element';
        }

        if ($model instanceof ModuleModel) {
            return 'Frontend module';
        }

        return 'Component';
    }
}
