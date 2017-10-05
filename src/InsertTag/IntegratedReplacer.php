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

namespace Netzmacht\Contao\Toolkit\InsertTag;

use Contao\InsertTags;

/**
 * IntegratedReplacer is the insert tag replacer implementation which integrates into the Contao replacement way.
 *
 * @package Netzmacht\Contao\I18n\InsertTag
 */
final class IntegratedReplacer implements Replacer
{
    /**
     * Insert tag map.
     *
     * @var Parser[]
     */
    private $parsers;

    /**
     * Insert tags replacer.
     *
     * @var InsertTags
     */
    private $insertTags;

    /**
     * Replacer constructor.
     *
     * @param InsertTags     $insertTags The insert tag replacer.
     * @param array|Parser[] $parsers    Insert tag parsers.
     */
    public function __construct($insertTags, array $parsers = array())
    {
        $this->parsers    = $parsers;
        $this->insertTags = $insertTags;
    }

    /**
     * {@inheritDoc}
     */
    public function registerParser(Parser $parser): Replacer
    {
        $this->parsers[] = $parser;

        return $this;
    }

    /**
     * Replace a single insert tag.
     *
     * This method is triggered by the replaceInsertTag hook.
     *
     * @param string $raw   Insert tag string.
     * @param bool   $cache Generate for the cache.
     *
     * @return bool|string
     */
    public function replaceTag(string $raw, bool $cache = true)
    {
        if (strpos($raw, '::')) {
            list($tag, $params) = explode('::', $raw, 2);
        } else {
            $tag    = $raw;
            $params = null;
        }

        foreach ($this->parsers as $parser) {
            if ($parser->supports($tag)) {
                return $parser->parse($raw, $tag, $params, $cache);
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function replace(string $content, bool $cache = true): string
    {
        return $this->insertTags->replace($content, $cache);
    }
}
