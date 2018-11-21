<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias\Exception;

use Netzmacht\Contao\Toolkit\Exception\RuntimeException;

/**
 * Class InvalidAliasException.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Exception
 */
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
    public static function forDatabaseEntry(string $tableName, int $rowId, $aliasValue): self
    {
        $message = sprintf(
            'Could not create unique alias for "%s::%s". Alias value "%s"',
            $tableName,
            $rowId,
            $aliasValue
        );

        return new static($message);
    }
}
