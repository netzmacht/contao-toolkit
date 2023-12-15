<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Doctrine\DBAL\Connection;

use function count;
use function explode;
use function sprintf;

/**
 * ForeignKeyFormatter formats fields which defines a foreign key.
 */
final class ForeignKeyFormatter implements ValueFormatter
{
    /**
     * Database connection.
     */
    private Connection $connection;

    /** @param Connection $database Database connection. */
    public function __construct(Connection $database)
    {
        $this->connection = $database;
    }

    /** {@inheritDoc} */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return isset($fieldDefinition['foreignKey']);
    }

    /** {@inheritDoc} */
    public function format(mixed $value, string $fieldName, array $fieldDefinition, mixed $context = null): mixed
    {
        $foreignKey = explode('.', $fieldDefinition['foreignKey'], 2);

        if (count($foreignKey) === 2) {
            $query  = sprintf('SELECT %s AS value FROM %s WHERE id=:id', $foreignKey[1], $foreignKey[0]);
            $result = $this->connection->executeQuery($query, ['id' => $value]);

            if ($result->rowCount()) {
                $value = $result->fetchOne();
            }
        }

        return $value;
    }
}
