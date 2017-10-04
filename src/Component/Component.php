<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\Component;

/**
 * The component is the interface describing content elements and modules.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
interface Component
{
    /**
     * Set the value of a parameter.
     *
     * @param string $name  Name of the parameter.
     * @param mixed  $value Value of the parameter.
     *
     * @return $this
     */
    public function set($name, $value);

    /**
     * Get the value of the parameter.
     *
     * @param string $name Parameter name.
     *
     * @return mixed
     */
    public function get($name);

    /**
     * Check if parameter exists.
     *
     * @param string $name Parameter name.
     *
     * @return bool
     */
    public function has($name);

    /**
     * Get the assigned model.
     *
     * @return \Model|null
     */
    public function getModel();

    /**
     * Generate the component.
     *
     * @return string
     */
    public function generate();
}
