<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

/**
 * The template renderer abstracts the task of rendering Twig templates.
 *
 * Supported template names are:
 *  - twig/template.html.twig
 *  - @Bundle/twig/template.html.twig
 */
interface TemplateRenderer
{
    /**
     * Render a template and get the result.
     *
     * @param string              $name       The template name. Supported formats are mentioned above.
     * @param array<string,mixed> $parameters Parameters passed to the template.
     */
    public function render(string $name, array $parameters = []): string;
}
