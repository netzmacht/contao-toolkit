<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Exception;

use Netzmacht\Contao\Toolkit\Exception\RuntimeException;

use function sprintf;

final class InvalidAliasException extends RuntimeException
{
    /**
     * Create exception for a database entry.
     *
     * @param string $tableName  The table name.
     * @param int    $rowId      Current row id.
     * @param mixed  $aliasValue Generated invalid alias.
     *
     * @return static
     */
    public static function forDatabaseEntry(string $tableName, int $rowId, mixed $aliasValue): self
    {
        $message = sprintf(
            'Could not create unique alias for "%s::%s". Alias value "%s"',
            $tableName,
            $rowId,
            $aliasValue,
        );

        return new static($message);
    }
}
