<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use ArrayAccess;
use Iterator;

/**
 * Interface Options describes the options.
 *
 * @extends ArrayAccess<array-key,mixed>
 * @extends Iterator<array-key,mixed>
 */
interface Options extends ArrayAccess, Iterator
{
    /**
     * Get array copy.
     *
     * @return array<string,array<string,mixed>|string>
     */
    public function getArrayCopy(): array;

    /**
     * Get the label column.
     */
    public function getLabelKey(): callable|string;

    /**
     * Get the value column.
     */
    public function getValueKey(): string;

    /**
     * Get the current row.
     *
     * @return array<string,mixed>
     */
    public function row(): array;
}
