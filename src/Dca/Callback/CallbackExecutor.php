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
class CallbackExecutor
{
    /**
     * The callback.
     *
     * @var mixed
     */
    private $callback;

    /**
     * CallbackHandler constructor.
     *
     * @param mixed $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * Handle the callback.
     *
     * @param mixed ... Various arguments.
     *
     * @return mixed
     */
    public function execute()
    {
        if (is_array($this->callback)) {
            $callback = \System::importStatic($this->callback[0]);

            return call_user_func_array([$callback, $this->callback[1]], func_get_args());
        }

        return call_user_func_array($this->callback, func_get_args());
    }
}
