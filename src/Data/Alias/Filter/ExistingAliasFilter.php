<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * Class ExistingAliasFilter uses the existing value.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
final class ExistingAliasFilter implements Filter
{
    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function repeatUntilValid(): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function breakIfValid(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($model, $value, string $separator)
    {
        return $value;
    }
}
