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
 * IntegratedReplacer is the insert tag replacer implementation which integrates into the Contao replacement way.
 *
 * @package Netzmacht\Contao\I18n\InsertTag
 */
class IntegratedReplacer implements Replacer
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
     * @var \Contao\InsertTags|InsertTags
     */
    private $insertTags;

    /**
     * Replacer constructor.
     *
     * @param \Contao\InsertTags|InsertTags $insertTags The insert tag replacer.
     * @param array|Parser[]                $parsers    Insert tag parsers.
     */
    public function __construct($insertTags, array $parsers = array())
    {
        $this->parsers    = $parsers;
        $this->insertTags = $insertTags;
    }

    /**
     * {@inheritDoc}
     */
    public function registerParser(Parser $parser)
    {
        $this->parsers[] = $parser;

        return $this;
    }

    /**
     * Get the replacer.
     *
     * This method is only designed to solve Contao lack of DI support! Don't use it. Use the service instead!
     *
     * @return static
     * @internal
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getInstance()
    {
        return $GLOBALS['container']['toolkit.insert-tag-replacer'];
    }

    /**
     * Replace a single insert tag.
     *
     * This method is triggered by the replaceInsertTag hook.
     *
     * @param string    $raw   Insert tag string.
     * @param bool|true $cache Generate for the cache.
     *
     * @return bool|string
     */
    public function replaceTag($raw, $cache = true)
    {
        list($tag, $params) = explode('::', $raw, 2);

        foreach ($this->parsers as $parser) {
            if ($parser->supports($tag)) {
                return $parser->parse($tag, $params, $raw, $cache);
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function replace($content, $cache = true)
    {
        return $this->insertTags->replace($content, $cache);
    }
}
