<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias\Filter;

use Netzmacht\Contao\Toolkit\Data\Alias\Filter;

/**
 * Class ExistingAliasFilter uses the existing value.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Filter
 */
class ExistingAliasFilter implements Filter
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
    public function repeatUntilUnique()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function breakIfUnique()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function apply($model, $value, $separator)
    {
        return $value;
    }
}
