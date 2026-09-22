<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\InsertTag;

use Netzmacht\Contao\Toolkit\InsertTag\AbstractInsertTagParser;
use Override;

final class ConcreteInsertTagParser extends AbstractInsertTagParser
{
    #[Override]
    protected function supports(string $tag, bool $cache): bool
    {
        return $tag === 'foo';
    }

    #[Override]
    protected function parseArguments(string $query): array
    {
        return [$query];
    }

    #[Override]
    protected function parseTag(array $arguments, string $tag, string $raw): bool|string
    {
        return 'parsed:' . $tag;
    }
}
