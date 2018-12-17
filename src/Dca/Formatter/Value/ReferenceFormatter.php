<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

/**
 * ReferenceFormatter formats fields which has a reference defined.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class ReferenceFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return !empty($fieldDefinition['reference']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        if (isset($fieldDefinition['reference'][$value])) {
            if (is_array($fieldDefinition['reference'][$value])) {
                list($value) = $fieldDefinition['reference'][$value];
            } else {
                $value = $fieldDefinition['reference'][$value];
            }
        }

        return $value;
    }
}
