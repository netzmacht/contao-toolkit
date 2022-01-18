<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Component\Exception;

use Contao\ContentModel;
use Contao\Database\Result;
use Contao\Model;
use Contao\ModuleModel;
use Netzmacht\Contao\Toolkit\Exception\Exception;
use RuntimeException;

use function sprintf;

/**
 * Exception class ContentElementNotFound is thrown if a content element could not be created.
 *
 * @deprecated Since 3.5.0 and get removed in 4.0.0
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

        return new self($message);
    }

    /**
     * Describe the model.
     *
     * @param Model|Result $model Component model.
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
