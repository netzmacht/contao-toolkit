<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Model;

/**
 * Class QueryProxy is designed as extension for the ContaoRepository to delegate all method calls to the model class.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Model
 */
trait QueryProxy
{
    /**
     * Call a method.
     *
     * @param string $name      Method name.
     * @param array  $arguments Arguments.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->call($name, $arguments);
    }
}
