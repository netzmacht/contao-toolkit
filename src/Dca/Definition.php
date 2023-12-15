<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca;

use function array_key_exists;
use function explode;
use function is_array;

/**
 * Definition provides easy access to the data container definition array.
 *
 * @psalm-suppress UnsupportedPropertyReferenceUsage
 */
final class Definition
{
    /**
     * The dca definition.
     *
     * @var array<string,mixed>
     */
    private array $dca;

    /**
     * Name of the data definition.
     */
    private string $name;

    /**
     * @param string              $name Name of the data definition.
     * @param array<string,mixed> $dca  The data definition array.
     */
    public function __construct(string $name, array &$dca)
    {
        $this->name = $name;
        $this->dca  =& $dca;
    }

    /**
     * Get the path as array.
     *
     * @param list<string|int>|string $path The path as "/" seared string or array.
     *
     * @return list<string|int>
     */
    private function path(array|string $path): array
    {
        if (! is_array($path)) {
            $path = explode('/', $path);
        }

        return $path;
    }

    /**
     * Get from the dca.
     *
     * @param string|list<string|int> $path              The path.
     * @param mixed|null              $default           The default value.
     * @param bool                    $createIfNotExists Create definition if not exists.
     */
    public function &get(array|string $path, mixed $default = null, bool $createIfNotExists = false): mixed
    {
        $dca =& $this->dca;

        foreach ($this->path($path) as $key) {
            if ($createIfNotExists && is_array($dca) && ! array_key_exists($key, $dca) && $default !== null) {
                $this->set($path, $default);
            }

            if (! is_array($dca) || ! array_key_exists($key, $dca)) {
                return $default;
            }

            $dca =& $dca[$key];
        }

        return $dca;
    }

    /**
     * Check if the definition has a configuration.
     *
     * @param string|list<string|int> $path The path as string or array.
     */
    public function has(array|string $path): bool
    {
        $dca =& $this->dca;

        foreach ($this->path($path) as $key) {
            if (! is_array($dca) || ! array_key_exists($key, $dca)) {
                return false;
            }

            $dca =& $dca[$key];
        }

        return true;
    }

    /**
     * Set a configuration in the data definition array.
     *
     * @param string|list<string|int> $path  The path as string or array.
     * @param mixed                   $value The value.
     */
    public function set(array|string $path, mixed $value): bool
    {
        $path = is_array($path) ? $path : explode('/', $path);
        /** @psalm-suppress UnsupportedPropertyReferenceUsage */
        $current =& $this->dca;
        $tmp     = null;

        foreach ($path as $key) {
            if (! is_array($current)) {
                return false;
            }

            if (! isset($current[$key])) {
                $current[$key] = [];
            }

            unset($tmp);
            $tmp =& $current;

            unset($current);
            $current =& $tmp[$key];
        }

        $current = $value;

        return true;
    }

    /**
     * Modify given configuration value.
     *
     * @param string|list<string|int> $path    The path as string or array.
     * @param callable                $handler A handler getting the current value passed and has to return the modified
     *                                         value.
     */
    public function modify(array|string $path, callable $handler): void
    {
        $this->set($path, $handler($this->get($path)));
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
