<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit;

use RuntimeException;

use function count;
use function implode;
use function set_error_handler;
use function sprintf;
use function str_contains;

use const E_USER_DEPRECATED;

/**
 * Shared phpspec helper to assert that a `trigger_deprecation()` call happened.
 *
 * Symfony's `trigger_deprecation()` reports via `@trigger_error($message, E_USER_DEPRECATED)`.
 * There is no symfony/phpunit-bridge in this project (phpspec, not PHPUnit), so this trait
 * installs a temporary error handler to capture those messages instead.
 */
trait DeprecationSpecHelper
{
    /** @return list<string> */
    private function captureDeprecations(callable $callback): array
    {
        $messages = [];
        $previous = set_error_handler(
            static function (int $errno, string $errstr) use (&$messages): bool {
                $messages[] = $errstr;

                return true;
            },
            E_USER_DEPRECATED,
        );

        try {
            $callback();
        } finally {
            set_error_handler($previous);
        }

        return $messages;
    }

    /** @param list<string> $messages */
    private function assertDeprecationTriggered(array $messages, string $needle): void
    {
        if (count($messages) === 0) {
            throw new RuntimeException('Expected a deprecation warning to be triggered, none was.');
        }

        foreach ($messages as $message) {
            if (str_contains($message, $needle)) {
                return;
            }
        }

        throw new RuntimeException(sprintf(
            'None of the %d captured deprecation message(s) contain "%s". Captured: %s',
            count($messages),
            $needle,
            implode(' | ', $messages),
        ));
    }
}
