<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Contao\FrontendTemplate as ContaoFrontendTemplate;
use Netzmacht\Contao\Toolkit\View\Template;

/**
 * FrontendTemplate with extended features.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class FrontendTemplate extends ContaoFrontendTemplate implements Template
{
    use TemplateTrait;

    /**
     * @param string                        $name        The template name.
     * @param array<string,object|callable> $helpers     View helpers.
     * @param string                        $contentType The content type.
     */
    public function __construct($name, $helpers = [], $contentType = 'text/html')
    {
        parent::__construct($name, $contentType);

        $this->helpers = $helpers;
    }
}
