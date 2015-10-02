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
 * Class HtmlValue.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class HtmlValue implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accept($fieldName, array $fieldDefinition, Definition $definition)
    {
        if (!empty($fieldDefinition['eval']['allowHtml'])) {
            return true;
        }

        if (!empty($fieldDefinition['eval']['preserveTags'])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, Definition $definition, $context = null)
    {
        return specialchars($value);
    }
}
