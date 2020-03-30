<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

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
    public function getValueKey(): string;

    /**
     * Get the current row.
     *
     * @return array
     */
    public function row(): array;
}
