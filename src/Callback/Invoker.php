<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Callback;

use Contao\CoreBundle\Framework\Adapter;
use Contao\System;
use InvalidArgumentException;

use function array_key_exists;
use function call_user_func_array;
use function is_array;

final class Invoker
{
    /** @param Adapter<System> $systemAdapter System adapter. */
    public function __construct(private readonly Adapter $systemAdapter)
    {
    }

    /**
     * Handle the callback.
     *
     * @param callable    $callback  Callback as Contao array notation or as PHP callable.
     * @param list<mixed> $arguments List of arguments being passed to the callback.
     *
     * @throws InvalidArgumentException On callback is not callable.
     */
    public function invoke(callable $callback, array $arguments = []): mixed
    {
        if (is_array($callback)) {
            $callback[0] = $this->systemAdapter->importStatic($callback[0]);
        }

        return call_user_func_array($callback, $arguments);
    }

    /**
     * Invoke a set of callbacks.
     *
     * @param callable[]  $callbacks        List of callbacks.
     * @param list<mixed> $arguments        Callback arguments.
     * @param bool|int    $returnValueIndex If the callback return value should be reused as an argument, give the
     *                                      index.
     *
     * @throws InvalidArgumentException On one callback is not callable.
     */
    public function invokeAll(array $callbacks, array $arguments = [], bool|int $returnValueIndex = false): mixed
    {
        $value = null;

        foreach ($callbacks as $callback) {
            $value = $this->invoke($callback, $arguments);

            if ($returnValueIndex === false) {
                continue;
            }

            if (! array_key_exists($returnValueIndex, $arguments)) {
                continue;
            }

            $arguments[$returnValueIndex] = $value;
        }

        return $value;
    }
}
