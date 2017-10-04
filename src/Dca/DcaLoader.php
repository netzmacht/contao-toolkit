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

namespace Netzmacht\Contao\Toolkit\Dca;

use Contao\Controller;

/**
 * Class Loader loads the data container.
 *
 * Internal used loader class. Do not used it by your own.
 *
 * @package Netzmacht\Contao\DevTools\Dca
 */
final class DcaLoader extends Controller
{
    /**
     * Construct.
     */
    // @codingStandardsIgnoreStart - Override it to make it public. Codesniffer is buggy here.
    public function __construct()
    {
        parent::__construct();
    }
    // @codingStandardsIgnoreEnd
}
