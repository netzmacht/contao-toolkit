<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Contao\FrontendTemplate as ContaoFrontendTemplate;
use Netzmacht\Contao\Toolkit\View\Template;

/**
 * FrontendTemplate with extended features.
 */
final class FrontendTemplate extends ContaoFrontendTemplate implements Template
{
    use TemplateTrait;

    /**
     * TemplateTrait constructor.
     *
     * @param string     $name        The template name.
     * @param callable[] $helpers     View helpers.
     * @param string     $contentType The content type.
     */
    public function __construct($name, $helpers = [], $contentType = 'text/html')
    {
        parent::__construct($name, $contentType);

        $this->helpers = $helpers;
    }
}
