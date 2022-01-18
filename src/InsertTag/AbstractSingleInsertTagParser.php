<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\InsertTag;

abstract class AbstractSingleInsertTagParser extends AbstractInsertTagParser
{
    /**
     * Tag name.
     *
     * @var string
     */
    protected $tagName;

    /**
     * If true caching is supported.
     *
     * @var bool
     */
    protected $supportsCaching = true;

    protected function supports(string $tag, bool $cache): bool
    {
        if ($tag !== $this->tagName) {
            return false;
        }

        return ! $cache || $this->supportsCaching;
    }
}
