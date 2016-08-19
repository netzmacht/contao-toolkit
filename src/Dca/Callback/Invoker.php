<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback;

/**
 * Class Callback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
class Invoker
{
    /**
     * Handle the callback.
     *
     * @param array|callable $callback  Callback as Contao array notation or as PHP callable.
     * @param array          $arguments List of arguments being passed to the callback.
     *
     * @return mixed
     */
    public function invoke($callback, array $arguments = [])
    {
        if (is_array($callback)) {
            $callback[0] = \System::importStatic($callback[0]);
        }

        return call_user_func_array($callback, $arguments);
    }
}
