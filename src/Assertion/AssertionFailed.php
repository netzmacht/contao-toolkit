<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\Assertion;

use Assert\InvalidArgumentException as BaseInvalidArgumentException;
use Netzmacht\Contao\Toolkit\Exception\Exception;

/**
 * Class InvalidArgumentException.
 *
 * @package Netzmacht\Contao\Toolkit\Assertion
 */
class AssertionFailed extends BaseInvalidArgumentException implements Exception
{
}
