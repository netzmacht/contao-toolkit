<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Definition;

/**
 * A ValueFormatter is responsible to format a field value.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formater\Value
 */
interface ValueFormatter
{
    /**
     * Check if the the formatter supports the field by the passed field name.
     *
     * @param string     $fieldName       Field name.
     * @param array      $fieldDefinition Field definition.
     * @param Definition $definition      Data container definition.
     *
     * @return bool
     */
    public function accept($fieldName, array $fieldDefinition, Definition $definition);

    /**
     * Format a field value.
     *
     * @param mixed      $value           Given value.
     * @param string     $fieldName       Field name.
     * @param array      $fieldDefinition Field definition.
     * @param Definition $definition      Data container definition.
     * @param mixed      $context         Context of the call. Usually the data container driver but not limited to.
     *
     * @return mixed
     */
    public function format($value, $fieldName, array $fieldDefinition, Definition $definition, $context = null);
}
