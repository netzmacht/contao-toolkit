<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * Class Callback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
final class Invoker
{
    /**
     * Handle the callback.
     *
     * @param array|callable $callback  Callback as Contao array notation or as PHP callable.
     * @param array          $arguments List of arguments being passed to the callback.
     *
     * @return mixed
     * @throws InvalidArgumentException On callback is not callable.
     */
    public function invoke($callback, array $arguments = [])
    {
        if (is_array($callback)) {
            $callback[0] = \System::importStatic($callback[0]);
        }

        Assert::isCallable($callback);

        return call_user_func_array($callback, $arguments);
    }

    /**
     * Invoke a set of callbacks.
     *
     * @param array|callable[] $callbacks        List of callbacks.
     * @param array            $arguments        Callback arguments.
     * @param int|string|bool  $returnValueIndex If the callback return value should be reused as an argument, give the
     *                                           index.
     *
     * @return mixed
     * @throws InvalidArgumentException On one callback is not callable.
     */
    public function invokeAll(array $callbacks, array $arguments = [], $returnValueIndex = false)
    {
        $value = null;

        foreach ($callbacks as $callback) {
            $value = $this->invoke($callback, $arguments);

            if ($returnValueIndex !== false) {
                $arguments[$returnValueIndex] = $value;
            }
        }

        return $value;
    }
}
