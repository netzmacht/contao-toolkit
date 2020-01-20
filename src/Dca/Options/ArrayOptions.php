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
 * Class ArrayOptions decorates an already existing options array with the Options interface.
 *
 * @package Netzmacht\Contao\DevTools\Dca\Options
 */
final class ArrayOptions extends \ArrayIterator implements Options
{
    /**
     * {@inheritdoc}
     */
    public function getLabelKey()
    {
        return '__label__';
    }

    /**
     * {@inheritdoc}
     */
    public function getValueKey(): string
    {
        return '__key__';
    }

    /**
     * {@inheritdoc}
     */
    public function row(): array
    {
        return [
            '__key__' => $this->key(),
            '__label__' => $this->current()
        ];
    }
}
