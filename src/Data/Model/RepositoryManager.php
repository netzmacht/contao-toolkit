<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Doctrine\DBAL\Connection;

interface RepositoryManager
{
    /**
     * Get a repository.
     *
     * @param string $modelClass Model class.
     */
    public function getRepository(string $modelClass): Repository;

    /**
     * Get the connection.
     */
    public function getConnection(): Connection;
}
