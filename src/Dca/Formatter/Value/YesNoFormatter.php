<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Override;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Class YesNoFormatter formats non multiple checkboxes with yes and no.
 */
final class YesNoFormatter implements ValueFormatter
{
    /**
     * Translator.
     */
    private Translator $translator;

    /** @param Translator $translator Translator. */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /** {@inheritDoc} */
    #[Override]
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        if (empty($fieldDefinition['inputType']) || $fieldDefinition['inputType'] !== 'checkbox') {
            return false;
        }

        if (empty($fieldDefinition['eval']['multiple'])) {
            return true;
        }

        return ! $fieldDefinition['eval']['multiple'];
    }

    /** {@inheritDoc} */
    #[Override]
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        return $this->translator->trans((empty($value) ? 'MSC.no' : 'MSC.yes'), [], 'contao_default');
    }
}
