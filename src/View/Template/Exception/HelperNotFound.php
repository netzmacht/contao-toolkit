<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template\Exception;

use Exception;
use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Throwable;

use function sprintf;

final class HelperNotFound extends RuntimeException
{
    /**
     * @param string    $helperName Name of the helper.
     * @param int       $code       Error code.
     * @param Exception $previous   Previous exception.
     */
    public function __construct($helperName, $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('No helper with name "%s" found.', $helperName);

        parent::__construct($message, $code, $previous);
    }
}
