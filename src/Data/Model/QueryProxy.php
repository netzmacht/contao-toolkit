<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

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
