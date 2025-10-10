<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Override;

final class HiddenValueFormatter implements ValueFormatter
{
    /**
     * Mask representation of a password.
     */
    private string $passwordMask;

    /** @param string $passwordMask A mask value for passwords. */
    public function __construct(string $passwordMask = '')
    {
        $this->passwordMask = $passwordMask;
    }

    /** {@inheritDoc} */
    #[Override]
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        if (! empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'password') {
            return true;
        }

        if (! empty($fieldDefinition['eval']['doNotShow'])) {
            return true;
        }

        return ! empty($fieldDefinition['eval']['hideInput']);
    }

    /** {@inheritDoc} */
    #[Override]
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        if ($this->passwordMask) {
            if (! empty($fieldDefinition['inputType']) && $fieldDefinition['inputType'] === 'password') {
                return $this->passwordMask;
            }
        }

        return '';
    }
}
