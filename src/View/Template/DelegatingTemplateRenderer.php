<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2021 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Twig\Environment;

use function preg_match;
use function sprintf;
use function substr;

/**
 * Class DelegatingTemplateRenderer support Twig and Contao templates and delegates the rendering to the engines.
 */
final class DelegatingTemplateRenderer implements TemplateRenderer
{
    /**
     * The Contao template factory.
     *
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * The twig environment.
     *
     * @var Environment|null
     */
    private $twig;

    /**
     * DelegatingTemplateRenderer constructor.
     *
     * @param TemplateFactory  $templateFactory The template factory.
     * @param Environment|null $twig            The twig environment. If twig is not activated it's null.
     */
    public function __construct(TemplateFactory $templateFactory, ?Environment $twig = null)
    {
        $this->templateFactory = $templateFactory;
        $this->twig            = $twig;
    }

    /**
     * {@inheritDoc}
     */
    public function render(string $name, array $parameters = []): string
    {
        if (substr($name, -5) === '.twig') {
            return $this->renderTwigTemplate($name, $parameters);
        }

        return $this->renderContaoTemplate($name, $parameters);
    }

    /**
     * Render a twig template.
     *
     * @param string $name       The template name.
     * @param array  $parameters The parameters.
     *
     * @return string
     *
     * @throws RuntimeException When twig is not available.
     */
    private function renderTwigTemplate(string $name, array $parameters): string
    {
        if ($this->twig === null) {
            throw new RuntimeException('Twig is not available');
        }

        return $this->twig->render($name, $parameters);
    }

    /**
     * Render a Contao template.
     *
     * @param string $name       The full template name.
     * @param array  $parameters The parameters.
     *
     * @return string
     *
     * @throws InvalidArgumentException When an unsupported template name is given.
     */
    private function renderContaoTemplate(string $name, array $parameters): string
    {
        [$scope, $templateName] = $this->extractScopeAndTemplateName($name);
        switch ($scope) {
            case 'fe':
                return $this->templateFactory->createFrontendTemplate($templateName, $parameters)->parse();

            case 'be':
                return $this->templateFactory->createBackendTemplate($templateName, $parameters)->parse();

            default:
                throw new InvalidArgumentException(sprintf('Template scope "%s" is not supported', $scope));
        }
    }

    /**
     * Extract the scope and template name from the whole template reference.
     *
     * @param string $name The template reference.
     *
     * @return array
     *
     * @throws InvalidArgumentException When an unsupported template name is given.
     */
    private function extractScopeAndTemplateName(string $name): array
    {
        if (!preg_match('/^(toolkit:)?(be|fe):([^.]+)(\.html5)?$/', $name, $matches)) {
            throw new InvalidArgumentException(sprintf('Template name "%s" is not supported', $name));
        }

        return [$matches[2], $matches[3]];
    }
}
