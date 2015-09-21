<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Component;

/**
 * Frontend module base class using the toolkit features.
 *
 * @package Netzmacht\Contao\Toolkit\Component
 */
abstract class Module extends \Module
{
    use ComponentTrait;
}
