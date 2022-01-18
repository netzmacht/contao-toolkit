<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use ArrayAccess;
use Iterator;

/**
 * Interface Options describes the options.
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
 */
interface Options extends ArrayAccess, Iterator
{
    /**
     * Get array copy.
     *
     * @return array<string,array<string,mixed>|string>
     */
    public function getArrayCopy();

    /**
     * Get the label column.
     *
     * @return string|callable
     */
    public function getLabelKey();

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
