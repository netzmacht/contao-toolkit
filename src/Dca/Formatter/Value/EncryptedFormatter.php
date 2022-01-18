<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Encryption;

/**
 * EncryptedFormatter decrypts encrypted values.
 */
final class EncryptedFormatter implements ValueFormatter
{
    /**
     * Encryption instance.
     *
     * @var Adapter<Encryption>
     */
    private $encryption;

    /**
     * @param Adapter<Encryption> $encryption Encryption service.
     */
    public function __construct(Adapter $encryption)
    {
        $this->encryption = $encryption;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return ! empty($fieldDefinition['eval']['encrypt']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        return $this->encryption->decrypt($value);
    }
}
