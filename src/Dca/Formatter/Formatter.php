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
    public function formatValue(string $field, $value, $context = null);

    /**
     * Format the field label.
     *
     * @param string $field Field name.
     *
     * @return string
     */
    public function formatFieldLabel(string $field): string;

    /**
     * Format the field description.
     *
     * @param string $field Field name.
     *
     * @return string
     */
    public function formatFieldDescription(string $field): string;

    /**
     * Format field options.
     *
     * @param string $field   Field name.
     * @param array  $values  Field values.
     * @param mixed  $context Data container object.
     *
     * @return array
     */
    public function formatOptions(string $field, array $values, $context = null): array;
}
