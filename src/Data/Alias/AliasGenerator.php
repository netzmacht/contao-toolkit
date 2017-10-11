<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database\Result;
use Contao\Model;

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
     * @return string|null
     */
    public function generate($result, $value = null);
}
