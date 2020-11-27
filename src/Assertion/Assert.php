<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Assertion;

use Assert\Assert as BaseAssert;

/**
 * Class Assert
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Assert extends BaseAssert
{
    /**
     * Lazy assertion exception class.
     *
     * @var string
     */
    protected static $lazyAssertionExceptionClass = LazyAssertionException::class;

    /**
     * Lazy assertion class.
     *
     * @var string
     */
    protected static $assertionClass = Assertion::class;
}
