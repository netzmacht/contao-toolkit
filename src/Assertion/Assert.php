<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Assertion;

use Assert\Assert as BaseAssert;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Assert extends BaseAssert
{
    /**
     * Lazy assertion exception class.
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected static $lazyAssertionExceptionClass = LazyAssertionException::class;

    /**
     * Lazy assertion class.
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
    protected static $assertionClass = Assertion::class;
}
