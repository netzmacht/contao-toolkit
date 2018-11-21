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

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database\Result;
use Contao\Model;

/**
 * Interface Validator describes an alias validator.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias
 */
interface Validator
{
    /**
     * Validate a value.
     *
     * @param Result|Model $result  The database result.
     * @param mixed        $value   Given value to validate.
     * @param array|null   $exclude Set of ids which should be ignored.
     *
     * @return bool
     */
    public function validate($result, $value, array $exclude = null): bool;
}
