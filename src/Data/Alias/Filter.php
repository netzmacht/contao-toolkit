<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias;


interface Filter
{
    public function repeatUntilUnique();

    public function breakIfUnique();

    public function initialize();

    public function apply($model, $value, $separator);
}
