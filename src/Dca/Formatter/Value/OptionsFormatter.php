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

use DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Callback\Invoker;

/**
 * OptionsFormatter fetches the value from the options or options callback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class OptionsFormatter implements ValueFormatter
{
    /**
     * Callback invoker.
     *
     * @var Invoker
     */
    private $invoker;

    /**
     * OptionsFormatter constructor.
     *
     * @param Invoker $invoker Callback invoker.
     */
    public function __construct(Invoker $invoker)
    {
        $this->invoker = $invoker;
    }

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
            if ($context instanceof DataContainer) {
                $options = $this->invoker->invoke($fieldDefinition['options_callback'], [$context]);
            } else {
                $options = $this->invoker->invoke($fieldDefinition['options_callback']);
            }

            if (!empty($options[$value])) {
                $value = $options[$value];
            }
        }

        return $value;
    }
}
