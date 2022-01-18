<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\StringUtil;

final class HtmlFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        if (! empty($fieldDefinition['eval']['allowHtml'])) {
            return true;
        }

        return ! empty($fieldDefinition['eval']['preserveTags']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        return StringUtil::specialchars($value);
    }
}
