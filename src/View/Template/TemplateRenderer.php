<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

/**
 * The template renderer abstracts the task of rendering templates.
 *
 * It replaces the templating component which is deprecated and removed from Symfony 5. The TemplateRenderer is a
 * wrapper for rendering toolkit based Contao templates and twig templates.
 *
 * Supported template names are:
 *  - be:be_main
 *  - fe:fe_page
 *  - toolkit:be:be_main.html5
 *  - toolkit:fe:fe_page.html5
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
