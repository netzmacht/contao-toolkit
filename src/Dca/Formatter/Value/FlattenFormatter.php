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

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

/**
 * FlattenFormatter takes an array and creates an csv value from it.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class FlattenFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return !empty($fieldDefinition['eval']['multiple']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        return $this->flatten($value);
    }

    /**
     * Flatten array or object to string values. Bypass anything else.
     *
     * This will create csv values from arrays and objects. For objects, it's public properties are used. Nested
     * arrays will be displayed with brackets.
     * $a = ['a', ['b', 'c']] will be a, [b, c].
     *
     * @param mixed $value    Current value.
     * @param bool  $brackets If true the value get brackets.
     *
     * @return array|string
     */
    private function flatten($value, bool $brackets = false)
    {
        if (is_array($value)) {
            $value = array_map(
                function ($value) {
                    return $this->flatten($value, true);
                },
                $value
            );

            $value = implode(', ', $value);
        } elseif (is_object($value)) {
            $value = $this->flatten(get_object_vars($value));
        } else {
            return $value;
        }

        if ($brackets) {
            $value = '[' . $value .']';
        }

        return $value;
    }
}
