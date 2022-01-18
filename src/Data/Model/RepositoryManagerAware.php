<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

interface RepositoryManagerAware
{
    /**
     * Register the repository manager.
     *
     * @param RepositoryManager $repositoryManager Repository manager.
     *
     * @return void
     *
     * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     */
    public function setRepositoryManager(RepositoryManager $repositoryManager);
}
