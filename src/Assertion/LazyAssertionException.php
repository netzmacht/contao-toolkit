<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Assertion;

use Assert\LazyAssertionException as BaseLazyAssertionException;
use Netzmacht\Contao\Toolkit\Exception\Exception;

class LazyAssertionException extends BaseLazyAssertionException implements Exception
{
}
