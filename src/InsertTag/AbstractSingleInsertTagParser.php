<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\InsertTag;

use Override;

abstract class AbstractSingleInsertTagParser extends AbstractInsertTagParser
{
    /**
     * Tag name.
     */
    protected string $tagName;

    /**
     * If true caching is supported.
     */
    protected bool $supportsCaching = true;

    #[Override]
    protected function supports(string $tag, bool $cache): bool
    {
        if ($tag !== $this->tagName) {
            return false;
        }

        return ! $cache || $this->supportsCaching;
    }
}
