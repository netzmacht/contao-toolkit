<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Helper;

use Netzmacht\Contao\Toolkit\View\ViewHelper;

/**
 * Class ViewHelperProxy.
 *
 * @package Netzmacht\Contao\Toolkit\View\Helper
 */
class ViewHelperProxy implements ViewHelper
{
    /**
     * Supported methods.
     *
     * @var array
     */
    private $methods = [];

    /**
     * Handler object.
     *
     * @var mixed
     */
    private $handler;

    /**
     * ViewHelperProxy constructor.
     *
     * @param mixed      $handler Handler object.
     * @param array|null $methods Map between helper name and triggered method.
     */
    public function __construct($handler, array $methods)
    {
        $this->handler = $handler;

        foreach ($methods as $helper => $method) {
            if (is_int($helper)) {
                $helper = $method;
            }

            $this->methods[$helper] = $method;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($method)
    {
        return isset($this->methods[$method]);
    }

    /**
     * {@inheritDoc}
     */
    public function __call($name, array $arguments)
    {
        $callback = [$this->handler, $name];

        return call_user_func_array($callback, $arguments);
    }
}
