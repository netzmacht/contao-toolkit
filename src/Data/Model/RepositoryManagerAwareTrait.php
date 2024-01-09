<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

trait RepositoryManagerAwareTrait
{
    /**
     * Repository manager.
     */
    protected RepositoryManager $repositoryManager;

    /**
     * Register the repository manager.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     */
    public function setRepositoryManager(RepositoryManager $repositoryManager): void
    {
        $this->repositoryManager = $repositoryManager;
    }
}
