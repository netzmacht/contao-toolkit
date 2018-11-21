<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template\Exception;

use Netzmacht\Contao\Toolkit\Exception\RuntimeException;

/**
 * Class HelperNotFound.
 *
 * @package Netzmacht\Contao\Toolkit\View
 */
final class HelperNotFound extends RuntimeException
{
    /**
     * HelperNotFound constructor.
     *
     * @param string     $helperName Name of the helper.
     * @param int        $code       Error code.
     * @param \Exception $previous   Previous exception.
     */
    public function __construct($helperName, $code = 0, \Exception $previous = null)
    {
        $message = sprintf('No helper with name "%s" found.', $helperName);

        parent::__construct($message, $code, $previous);
    }
}
