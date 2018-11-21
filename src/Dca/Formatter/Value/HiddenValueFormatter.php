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
 * Class HiddenValueFormatter.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class HiddenValueFormatter implements ValueFormatter
{
    /**
     * Mask representation of a password.
     *
     * @var string
     */
    private $passwordMask;

    /**
     * HiddenValueFormatter constructor.
     *
     * @param string $passwordMask A mask value for passwords.
     */
    public function __construct($passwordMask = '')
    {
        $this->passwordMask = $passwordMask;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        if (!empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'password') {
            return true;
        }

        if (!empty($fieldDefinition['eval']['doNotShow'])) {
            return true;
        }

        if (!empty($fieldDefinition['eval']['hideInput'])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        if ($this->passwordMask) {
            if (!empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'password') {
                return $this->passwordMask;
            }
        }

        return '';
    }
}
