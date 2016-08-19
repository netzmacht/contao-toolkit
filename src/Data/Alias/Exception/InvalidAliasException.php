<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Data\Alias\Exception;

use Netzmacht\Contao\Toolkit\Exception;

/**
 * Class InvalidAliasException.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias\Exception
 */
class InvalidAliasException extends Exception
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
    public static function forDatabaseEntry($tableName, $rowId, $aliasValue)
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
