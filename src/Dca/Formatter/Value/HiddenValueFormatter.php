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

/**
 * Class HiddenValueFormatter.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class HiddenValueFormatter implements ValueFormatter
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
    public function accepts($fieldName, array $fieldDefinition)
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
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        if ($this->passwordMask) {
            if (!empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'password') {
                return $this->passwordMask;
            }
        }

        return '';
    }
}
