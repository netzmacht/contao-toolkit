<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Assertion;

use Assert\Assertion as BaseAssertion;

class Assertion extends BaseAssertion
{
    /**
     * Exception class.
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected static $exceptionClass = AssertionFailed::class;
}
