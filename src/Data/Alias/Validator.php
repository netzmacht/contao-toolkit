<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias;

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
     * @param mixed      $value   Given value to validate.
     * @param array|null $exclude Set of ids which should be ignored.
     *
     * @return bool
     */
    public function validate($value, array $exclude = null);
}
