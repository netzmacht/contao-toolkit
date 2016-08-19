<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Database;

/**
 * ForeignKeyFormatter formats fields which defines a foreign key.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class ForeignKeyFormatter implements ValueFormatter
{
    /**
     * Database connection.
     *
     * @var Database
     */
    private $database;

    /**
     * ForeignKeyFormatter constructor.
     *
     * @param Database $database Database connection.
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts($fieldName, array $fieldDefinition)
    {
        return isset($fieldDefinition['foreignKey']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        $foreignKey = explode('.', $fieldDefinition['foreignKey'], 2);

        if (count($foreignKey) == 2) {
            $query  = sprintf('SELECT %s AS value FROM %s WHERE id=?', $foreignKey[1], $foreignKey[0]);
            $result = $this->database->prepare($query)->execute($value);

            if ($result->numRows) {
                $value = $result->value;
            }
        }

        return $value;
    }
}
