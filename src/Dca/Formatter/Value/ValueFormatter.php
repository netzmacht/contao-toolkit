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

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

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
     * @param string $fieldName       Field name.
     * @param array  $fieldDefinition Field definition.
     *
     * @return bool
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool;

    /**
     * Format a field value.
     *
     * @param mixed  $value           Given value.
     * @param string $fieldName       Field name.
     * @param array  $fieldDefinition Field definition.
     * @param mixed  $context         Context of the call. Usually the data container driver but not limited to.
     *
     * @return mixed
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null);
}
