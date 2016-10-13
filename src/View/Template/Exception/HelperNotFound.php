<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Template\Exception;

use Netzmacht\Contao\Toolkit\Exception;

/**
 * Class HelperNotFound.
 *
 * @package Netzmacht\Contao\Toolkit\View
 */
class HelperNotFound extends Exception
{
    /**
     * HelperNotFound constructor.
     *
     * @param string    $helperName Name of the helper.
     * @param int       $code       Error code.
     * @param Exception $previous   Previous exception.
     */
    public function __construct($helperName, $code = 0, Exception $previous = null)
    {
        $message = sprintf('No helper with name "%s" found.', $helperName);

        parent::__construct($message, $code, $previous);
    }
}
