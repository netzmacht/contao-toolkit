<?php

/**
 * @package    toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template;

/**
 * BackendTemplate with extended features.
 */
final class BackendTemplate extends \Contao\BackendTemplate implements Template
{
    use TemplateTrait;
}
