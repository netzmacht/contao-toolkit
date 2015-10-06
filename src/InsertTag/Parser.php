<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\InsertTag;

/**
 * Insert Tag parser parses an insert tag.
 *
 * @package Netzmacht\Contao\I18n\InsertTag
 */
interface Parser
{
    /**
     * Get the supported tags.
     *
     * @return array
     */
    public static function getTags();

    /**
     * Parse an insert tag.
     *
     * @param string    $raw    Raw insert tag.
     * @param string    $tag    Insert tag name before the first :: separator.
     * @param string    $params Insert tag params, all after the first :: separator.
     * @param bool|true $cache  Check if the insert tags get cached.
     *
     * @return string
     */
    public function parse($raw, $tag, $params = null, $cache = true);

    /**
     * Check if an tag is supported.
     *
     * @param string $tag Tag name.
     *
     * @return bool
     */
    public function supports($tag);
}
