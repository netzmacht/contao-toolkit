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
 * Exception class ModuleNotFound is thrown if a frontend module could not be created.
 *
 * @package Netzmacht\Contao\Toolkit\Component\ContentElement
 */
class ModuleNotFound extends \Exception
{
    /**
     * Create exception for the given model.
     *
     * @param \ModuleModel $moduleModel Module model.
     *
     * @return static
     */
    public static function forModel($moduleModel)
    {
        $message = sprintf(
            'Frontend module of type "%s" not found for model ID %s',
            $moduleModel->type,
            $moduleModel->id
        );

        return new static($message);
    }
}
