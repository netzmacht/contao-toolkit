<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template\Exception;

use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Throwable;

use function sprintf;

/**
 * @deprecated Part of the deprecated legacy template component. Will be removed in 5.0.
 */
final class HelperNotFound extends RuntimeException
{
    /**
     * @param string         $helperName Name of the helper.
     * @param int            $code       Error code.
     * @param Throwable|null $previous   Previous exception.
     */
    public function __construct(string $helperName, int $code = 0, Throwable|null $previous = null)
    {
        $message = sprintf('No helper with name "%s" found.', $helperName);

        parent::__construct($message, $code, $previous);
    }
}
