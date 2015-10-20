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

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Callback\CallbackExecutor;

/**
 * OptionsFormatter fetches the value from the options or options callback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class OptionsFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts($fieldName, array $fieldDefinition)
    {
        if (!empty($fieldDefinition['eval']['isAssociative']) || !empty($fieldDefinition['options'])) {
            return true;
        }

        return !empty($fieldDefinition['options_callback']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        if (!empty($fieldDefinition['eval']['isAssociative'])
            || (!empty($fieldDefinition['options']) && array_is_assoc($fieldDefinition['options']))
        ) {
            if (!empty($fieldDefinition['options'][$value])) {
                $value = $fieldDefinition['options'][$value];
            }
        } elseif (!empty($fieldDefinition['options_callback'])) {
            $callback = new CallbackExecutor($fieldDefinition['options_callback']);

            if ($context instanceof DataContainer) {
                $options = $callback->execute($context);
            } else {
                $options = $callback->execute();
            }

            if (!empty($options[$value])) {
                $value = $options[$value];
            }
        }

        return $value;
    }
}
