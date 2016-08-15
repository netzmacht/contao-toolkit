<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Helper;

/**
 * Class TemplateHelper.
 *
 * @package Netzmacht\Contao\Toolkit\View
 */
final class ViewHelper
{
    /**
     * Map of registered helper factories.
     *
     * @var array
     */
    private $map = [];

    /**
     * Created helper instances.
     *
     * @var array
     */
    private $helpers = [];

    /**
     * TemplateHelper constructor.
     *
     * @param array $map
     */
    public function __construct(array $map = [])
    {
        $this->map = $map;
    }

    /**
     * Call a helper.
     *
     * @param string $helper    Name of the helper.
     * @param array  $arguments Arguments of the called helper method.
     *
     * @return mixed
     * @throws HelperNotFound If no helper is found.
     */
    public function call($helper, array $arguments = [])
    {
        if (!isset($this->map[$helper])) {
            throw new HelperNotFound($helper);
        }

        if (!isset($this->helpers[$helper])) {
            $factory                = $this->map[$helper];
            $this->helpers[$helper] = $factory();
        }

        return call_user_func_array($this->helpers[$helper], $arguments);
    }

    /**
     * Support magic calls.
     *
     * @param string $name    Name of the helper.
     * @param array  $arguments Arguments of the called helper method.
     *
     * @return mixed
     */
    function __call($name, array $arguments)
    {
        return $this->call($name, $arguments);
    }
}
