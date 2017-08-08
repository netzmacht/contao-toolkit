<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Doctrine\DBAL\Connection;

/**
 * ForeignKeyFormatter formats fields which defines a foreign key.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class ForeignKeyFormatter implements ValueFormatter
{
    /**
     * Database connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * ForeignKeyFormatter constructor.
     *
     * @param Connection $database Database connection.
     */
    public function __construct(Connection $database)
    {
        $this->connection = $database;
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
            $query     = sprintf('SELECT %s AS value FROM %s WHERE id=:id', $foreignKey[1], $foreignKey[0]);
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $value);

            if ($statement->execute() && $statement->rowCount()) {
                $value = $statement->fetchColumn('value');
            }
        }

        return $value;
    }
}
