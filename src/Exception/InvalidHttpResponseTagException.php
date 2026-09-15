<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Exception;

use InvalidArgumentException;

/**
 * Class InvalidHttpResponseTagException is thrown when applying tags to response failed
 *
 * @deprecated Part of the deprecated ResponseTagger encapsulation. Will be removed in 5.0.
 */
final class InvalidHttpResponseTagException extends InvalidArgumentException implements Exception
{
}
