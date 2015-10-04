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

class OptionsFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accept($fieldName, array $fieldDefinition)
    {
        if (!empty($fieldDefinition['isAssociative']) || !empty($fieldDefinition['options'])) {
            return true;
        }

        return !empty($fieldDefinition['options_callback']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        if (!empty($fieldDefinition['isAssociative']) || array_is_assoc($fieldDefinition['options'])) {
            if (!empty($fieldDefinition['options'][$value])) {
                $value = $fieldDefinition['options'][$value];
            }
        } elseif (!empty($fieldDefinition['options_callback'])) {
            $callback = new CallbackExecutor($fieldDefinition['options_callback']);

            if ($context instanceof DataContainer) {
                $value = $callback->execute($context);
            } else {
                $value = $callback->execute();
            }
        }

        return $value;
    }
}
