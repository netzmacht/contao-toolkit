<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Formatter;

/**
 * Interface describes the data container definition manager.
 */
interface DcaManager
{
    /**
     * Get a data container definition.
     *
     * @param string $name    The data definition name.
     * @param bool   $noCache If true not the cached version is loaded.
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getDefinition(string $name, bool $noCache = false): Definition;

    /**
     * Get a formatter for a definition.
     *
     * @param string $name Definition or name.
     */
    public function getFormatter(string $name): Formatter;
}
