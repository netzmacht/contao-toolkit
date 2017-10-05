<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\InsertTag;

/**
 * Insert Tag parser parses an insert tag.
 *
 * @package Netzmacht\Contao\I18n\InsertTag
 */
interface Parser
{
    /**
     * Parse an insert tag.
     *
     * @param string $raw    Raw insert tag.
     * @param string $tag    Insert tag name before the first :: separator.
     * @param string $params Insert tag params, all after the first :: separator.
     * @param bool   $cache  Check if the insert tags get cached.
     *
     * @return string
     */
    public function parse(string $raw, string $tag, string $params = null, bool $cache = true): string;

    /**
     * Check if an tag is supported.
     *
     * @param string $tag Tag name.
     *
     * @return bool
     */
    public function supports(string $tag): bool;
}
