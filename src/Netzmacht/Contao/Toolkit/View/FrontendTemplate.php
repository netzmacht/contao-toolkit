<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View;

use Netzmacht\Contao\Toolkit\TranslatorTrait;

/**
 * FrontendTemplate with extended features.
 */
class FrontendTemplate extends \FrontendTemplate implements Template
{
    use TranslatorTrait;
    use TemplateTrait;
}
