<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Date;
use Override;

use function in_array;

/**
 * DateFormatter format date values.
 */
final class DateFormatter implements ValueFormatter
{
    /** @param Adapter<Config> $config Contao config. */
    public function __construct(private readonly Adapter $config)
    {
    }

    /** {@inheritDoc} */
    #[Override]
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        if ($fieldName === 'tstamp') {
            return true;
        }

        if (empty($fieldDefinition['eval']['rgxp'])) {
            return false;
        }

        return in_array($fieldDefinition['eval']['rgxp'], ['date', 'datim', 'time']);
    }

    /** {@inheritDoc} */
    #[Override]
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        if (empty($fieldDefinition['eval']['rgxp'])) {
            $format = 'datim';
        } else {
            $format = $fieldDefinition['eval']['rgxp'];
        }

        $dateFormat = $this->config->get($format . 'Format');

        return Date::parse($dateFormat, $value);
    }
}
