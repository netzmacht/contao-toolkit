<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2014 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\DevTools\Dca\Options;

/**
 * Class ArrayOptions decorates an already existing options array with the Options interface.
 *
 * @package Netzmacht\Contao\DevTools\Dca\Options
 */
class ArrayOptions extends \ArrayIterator implements Options
{
}
