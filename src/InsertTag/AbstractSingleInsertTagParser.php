<?php

/**
 * Contao Toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\InsertTag;

/**
 * Class AbstractSingleInsertTagParser.
 *
 * @package Netzmacht\Contao\Toolkit\InsertTag
 */
abstract class AbstractSingleInsertTagParser extends AbstractInsertTagParser
{
    /**
     * Tag name.
     *
     * @var string
     */
    protected $tagName;

    /**
     * If true caching is supported.
     *
     * @var bool
     */
    protected $supportsCaching = true;

    /**
     * {@inheritdoc}
     */
    protected function supports(string $tag, bool $cache): bool
    {
        if ($tag !== $this->tagName) {
            return false;
        }

        if ($cache && !$this->supportsCaching) {
            return false;
        }

        return true;
    }
}
