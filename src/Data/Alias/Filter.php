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

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database\Result;
use Contao\Model;

/**
 * Filter modifies a value for the alias generator.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Alias
 */
interface Filter
{
    /**
     * If true the filter can be applied until an unique value is generated.
     *
     * @return bool
     */
    public function repeatUntilValid();

    /**
     * If true no ongoing filters get applied.
     *
     * @return bool
     */
    public function breakIfValid();

    /**
     * Initialize the filter.
     *
     * @return void
     */
    public function initialize();

    /**
     * Apply the filter.
     *
     * @param Model|Result $model     Current model.
     * @param mixed        $value     Current value.
     * @param string       $separator Separator character between different alias tokens.
     *
     * @return string
     */
    public function apply($model, $value, $separator);
}
