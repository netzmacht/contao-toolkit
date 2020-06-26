<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */
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
     * @return Definition
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getDefinition(string $name, bool $noCache = false): Definition;

    /**
     * Get a formatter for a definition.
     *
     * @param Definition|string $name Definition or name.
     *
     * @return Formatter
     */
    public function getFormatter(string $name): Formatter;
}
