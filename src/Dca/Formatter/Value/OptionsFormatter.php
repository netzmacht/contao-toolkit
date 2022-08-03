<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Callback\Invoker;

use function array_keys;
use function count;
use function is_array;
use function range;

/**
 * OptionsFormatter fetches the value from the options or options callback.
 */
final class OptionsFormatter implements ValueFormatter
{
    /**
     * Callback invoker.
     */
    private Invoker $invoker;

    /**
     * @param Invoker $invoker Callback invoker.
     */
    public function __construct(Invoker $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        if (! empty($fieldDefinition['eval']['isAssociative']) || ! empty($fieldDefinition['options'])) {
            return true;
        }

        return ! empty($fieldDefinition['options_callback']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        if (
            ! empty($fieldDefinition['eval']['isAssociative'])
            || (! empty($fieldDefinition['options']) && $this->isAssociativeArray($fieldDefinition['options']))
        ) {
            if (! empty($fieldDefinition['options'][$value])) {
                $value = $fieldDefinition['options'][$value];
            }
        } elseif (! empty($fieldDefinition['options_callback'])) {
            if ($context instanceof DataContainer) {
                $options = $this->invoker->invoke($fieldDefinition['options_callback'], [$context]);
            } else {
                $options = $this->invoker->invoke($fieldDefinition['options_callback']);
            }

            if (! empty($options[$value])) {
                $value = $options[$value];
            }
        }

        return $value;
    }

    /**
     * Check if given value is an associative array.
     *
     * @param mixed $value Given value.
     */
    private function isAssociativeArray($value): bool
    {
        return is_array($value) && array_keys($value) !== range(0, count($value) - 1);
    }
}
