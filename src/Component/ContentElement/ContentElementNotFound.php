<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component\ContentElement;

/**
 * Exception class ContentElementNotFound is thrown if a content element could not be created.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
class ContentElementNotFound extends \Exception
{
    /**
     * Create exception for the given model.
     *
     * @param \ContentModel $contentModel Content element model.
     *
     * @return static
     */
    public static function forModel($contentModel)
    {
        $message = sprintf(
            'Content element of type "%s" not found for model ID %s',
            $contentModel->type,
            $contentModel->id
        );

        return new static($message);
    }
}
