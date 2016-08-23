<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Options;

/**
 * Interface Options describes the options.
 *
 * @package Netzmacht\Contao\DevTools\Dca\Options
 */
interface Options extends \ArrayAccess, \Iterator
{
    /**
     * Get array copy.
     *
     * @return array
     */
    public function getArrayCopy();

    /**
     * Get the label column.
     *
     * @return string|callable
     */
    public function getLabelKey();

    /**
     * Get the value column.
     *
     * @return string
     */
    public function getValueKey();

    /**
     * Get the current row.
     *
     * @return array
     */
    public function row();
}
