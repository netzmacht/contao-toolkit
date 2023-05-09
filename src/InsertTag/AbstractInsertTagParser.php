<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\InsertTag;

use function explode;

abstract class AbstractInsertTagParser
{
    /**
     * Replace an insert tag.
     *
     * @param string $raw   The insert tag string.
     * @param bool   $cache If false non cacheable tags should are replaced.
     *
     * @return string|false
     */
    public function replace(string $raw, bool $cache = true): bool|string
    {
        $parts = explode('::', $raw, 2);
        $tag   = $parts[0];

        if (! $this->supports($tag, $cache)) {
            return false;
        }

        $arguments = $this->parseArguments($parts[1]);

        return $this->parseTag($arguments, $tag, $raw);
    }

    /**
     * Test if insert tag is supported.
     *
     * @param string $tag   The tag name.
     * @param bool   $cache If false non cacheable tags should are replaced.
     */
    abstract protected function supports(string $tag, bool $cache): bool;

    /**
     * Parse insert tag query.
     *
     * @param string $query Argument query.
     *
     * @return array<string|int,mixed>
     */
    abstract protected function parseArguments(string $query): array;

    /**
     * Parse the insert tag.
     *
     * @param array<string|int,mixed> $arguments Insert tag arguments.
     * @param string                  $tag       Insert tag name.
     * @param string                  $raw       The raw insert tag string.
     *
     * @return string|false
     */
    abstract protected function parseTag(array $arguments, string $tag, string $raw): bool|string;
}
