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

class ExistingAliasFilter implements Filter
{
    public function initialize()
    {
    }

    public function repeatUntilUnique()
    {
        return false;
    }

    public function breakIfUnique()
    {
        return true;
    }

    public function apply($model, $value, $separator)
    {
        return $value;
    }
}
