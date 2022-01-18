<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Assertion;

use Assert\InvalidArgumentException as BaseInvalidArgumentException;
use Netzmacht\Contao\Toolkit\Exception\Exception;

class AssertionFailed extends BaseInvalidArgumentException implements Exception
{
}
