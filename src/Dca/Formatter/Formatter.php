<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

/**
 * Formatter handles the formatting of data container labels.
 *
 * @package Netzmacht\Contao\Toolkit\Dca
 */
interface Formatter
{
    /**
     * Format a field value.
     *
     * @param string $field   Field name.
     * @param mixed  $value   Field value.
     * @param mixed  $context Context object, usually the data container driver.
     *
     * @return array|null|string
     */
    public function formatValue($field, $value, $context = null);

    /**
     * Format the field label.
     *
     * @param string $field Field name.
     *
     * @return string
     */
    public function formatFieldLabel($field);

    /**
     * Format the field description.
     *
     * @param string $field Field name.
     *
     * @return mixed
     */
    public function formatFieldDescription($field);

    /**
     * Format field options.
     *
     * @param string $field   Field name.
     * @param array  $values  Field values.
     * @param mixed  $context Data container object.
     *
     * @return array
     */
    public function formatOptions($field, array $values, $context = null);
}
