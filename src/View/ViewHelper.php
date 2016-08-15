<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View;

/**
 * Interface ViewHelper.
 *
 * @package Netzmacht\Contao\Toolkit\View\Helper
 */
interface ViewHelper
{
    /**
     * Check if view helper plugin supports an call.
     *
     * @param string $method Method name.
     *
     * @return bool
     */
    public function supports($method);

    /**
     * Handle the view helper call.
     *
     * @param string $name      Name of the view helper.
     * @param array  $arguments Arguments of the view helper.
     *
     * @return mixed
     */
    public function __call($name, array $arguments);
}
