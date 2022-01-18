<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Exception;

use InvalidArgumentException;

/**
 * Class InvalidHttpResponseTagException is thrown when applying tags to response failed
 */
final class InvalidHttpResponseTagException extends InvalidArgumentException implements Exception
{
}
