<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\DependencyInjection\Listener;

use Contao\Model;

final class RegisterContaoModelsListener
{
    /** @param array<string,class-string<Model>> $repositories List of repositories. */
    public function __construct(private readonly array $repositories)
    {
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
