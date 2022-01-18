<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template;

/**
 * TemplateFactory creates a template with some predefined helpers.
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
 */
interface TemplateFactory
{
    /**
     * Create a frontend template.
     *
     * @param string                   $name        Template name.
     * @param array<string,mixed>|null $data        Template data.
     * @param string                   $contentType Content type.
     */
    public function createFrontendTemplate(
        string $name,
        array $data = null,
        string $contentType = 'text/html'
    ): Template;

    /**
     * Create a backend template.
     *
     * @param string                   $name        Template name.
     * @param array<string,mixed>|null $data        Template data.
     * @param string                   $contentType Content type.
     */
    public function createBackendTemplate(
        string $name,
        array $data = null,
        string $contentType = 'text/html'
    ): Template;
}
