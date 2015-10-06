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

use Contao\Controller;

/**
 * Class InsertTags allows to replace insert tags.
 *
 * This class is an internal used helper to access the replaceInsertTags methods of Contao. Since Contao 3.5.3 the
 * provided Contao\InsertTags class of Contao is used.
 *
 * @internal
 * @package Netzmacht\Contao\Toolkit\InsertTag
 */
class InsertTags extends Controller
{
    /**
     * Construct.
     */
    public function __construct()
    {
        \Database::getInstance();
        parent::__construct();
    }

    /**
     * Replace insert tags with their values
     *
     * @param string  $buffer The text with the tags to be replaced.
     * @param boolean $cache  If false, non-cacheable tags will be replaced.
     *
     * @return string The text with the replaced tags
     */
    public function replace($buffer, $cache = true)
    {
        return $this->replaceInsertTags($buffer, $cache);
    }
}
