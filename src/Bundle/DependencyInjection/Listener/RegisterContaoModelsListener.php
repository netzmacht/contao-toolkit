<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\Listener;

use Contao\Model;

final class RegisterContaoModelsListener
{
    /**
     * List of repositories.
     *
     * @var array<string,class-string<Model>>
     */
    private array $repositories;

    /**
     * @param array<string,class-string<Model>> $repositories List of repositories.
     */
    public function __construct(array $repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * Handle the on initialize system hook.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function onInitializeSystem(): void
    {
        foreach ($this->repositories as $table => $modelClass) {
            $GLOBALS['TL_MODELS'][$table] = $modelClass;
        }
    }
}
