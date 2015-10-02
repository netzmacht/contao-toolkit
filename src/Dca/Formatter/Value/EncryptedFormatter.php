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
 * EncryptedFormatter decrypts encrypted values.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class EncryptedFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accept($fieldName, array $fieldDefinition, Definition $definition)
    {
        return !empty($fieldDefinition['eval']['encrypt']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, Definition $definition, $context = null)
    {
        return \Encryption::decrypt($value);
    }
}
