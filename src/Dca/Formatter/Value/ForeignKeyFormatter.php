<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

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
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return isset($fieldDefinition['foreignKey']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        $foreignKey = explode('.', $fieldDefinition['foreignKey'], 2);

        if (count($foreignKey) == 2) {
            $query     = sprintf('SELECT %s AS value FROM %s WHERE id=:id', $foreignKey[1], $foreignKey[0]);
            $statement = $this->connection->prepare($query);
            $statement->bindValue('id', $value);

            if ($statement->execute() && $statement->rowCount()) {
                $value = $statement->fetchColumn(0);
            }
        }

        return $value;
    }
}
