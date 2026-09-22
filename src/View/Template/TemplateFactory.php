<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template;

/**
 * TemplateFactory creates a template with some predefined helpers.
 *
 * @deprecated Use native Twig templates instead. Will be removed in 5.0.
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
        array|null $data = null,
        string $contentType = 'text/html',
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
        array|null $data = null,
        string $contentType = 'text/html',
    ): Template;
}
