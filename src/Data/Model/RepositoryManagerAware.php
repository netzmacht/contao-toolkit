<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

interface RepositoryManagerAware
{
    /**
     * Register the repository manager.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function setRepositoryManager(RepositoryManager $repositoryManager): void;
}
