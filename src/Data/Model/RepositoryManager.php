<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;
use Doctrine\DBAL\Connection;

interface RepositoryManager
{
    /**
     * Get a repository.
     *
     * @template T of Model
     *
     * @param class-string<T> $modelClass Model class.
     *
     * @return Repository<T>
     */
    public function getRepository(string $modelClass): Repository;

    /**
     * Get the connection.
     */
    public function getConnection(): Connection;
}
