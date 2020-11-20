<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     Christopher Bölter <christopher@boelter.eu>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

/**
 * The template renderer abstracts the task of rendering templates.
 *
 * It replaces the templating component which is deprecated and removed from Symfony 5. The TemplateRenderer is a
 * wrapper for rendering toolkit based Contao templates and twig templates.
 *
 * Supported template names are:
 *  - toolkit:be:be_main
 *  - toolkit:fe:fe_page
 *  - twig/template.html.twig
 *  - @Bundle/twig/template.html.twig
 */
interface TemplateRenderer
{
    /**
     * Render a template and get the result.
     *
     * @param string $name       The template name. Supported formats are mentioned above.
     * @param array  $parameters Parameters passed to the template.
     *
     * @return string
     */
    public function render(string $name, array $parameters = []): string;
}
