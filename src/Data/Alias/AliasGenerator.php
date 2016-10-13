<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Database\Result;
use Model;

/**
 * Alias generator interface.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias
 */
interface AliasGenerator
{
    /**
     * Generate the alias.
     *
     * @param Result|Model $result The database result.
     * @param mixed        $value  The current value.
     *
     * @return mixed|null|string
     */
    public function generate($result, $value = null);
}
