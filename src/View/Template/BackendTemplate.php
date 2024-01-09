<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Contao\BackendTemplate as ContaoBackendTemplate;
use Netzmacht\Contao\Toolkit\View\Template;

/**
 * BackendTemplate with extended features.
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class BackendTemplate extends ContaoBackendTemplate implements Template
{
    use TemplateTrait;

    /**
     * @param string                        $name        The template name.
     * @param array<string,object|callable> $helpers     View helpers.
     * @param string                        $contentType The content type.
     */
    public function __construct(string $name, array $helpers = [], string $contentType = 'text/html')
    {
        parent::__construct($name, $contentType);

        $this->helpers = $helpers;
    }
}
