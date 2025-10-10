<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\StringUtil;
use Override;

final class HtmlFormatter implements ValueFormatter
{
    /** {@inheritDoc} */
    #[Override]
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        if (! empty($fieldDefinition['eval']['allowHtml'])) {
            return true;
        }

        return ! empty($fieldDefinition['eval']['preserveTags']);
    }

    /** {@inheritDoc} */
    #[Override]
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        return StringUtil::specialchars($value);
    }
}
