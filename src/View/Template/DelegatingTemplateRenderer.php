<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Twig\Environment;

use function preg_match;
use function sprintf;
use function str_ends_with;

/**
 * Class DelegatingTemplateRenderer support Twig and Contao templates and delegates the rendering to the engines.
 */
final class DelegatingTemplateRenderer implements TemplateRenderer
{
    /**
     * The Contao template factory.
     */
    private TemplateFactory $templateFactory;

    /**
     * The twig environment.
     */
    private Environment|null $twig;

    /**
     * @param TemplateFactory  $templateFactory The template factory.
     * @param Environment|null $twig            The twig environment. If twig is not activated it's null.
     */
    public function __construct(TemplateFactory $templateFactory, Environment|null $twig = null)
    {
        $this->templateFactory = $templateFactory;
        $this->twig            = $twig;
    }

    /** {@inheritDoc} */
    public function render(string $name, array $parameters = []): string
    {
        if (str_ends_with($name, '.twig')) {
            return $this->renderTwigTemplate($name, $parameters);
        }

        return $this->renderContaoTemplate($name, $parameters);
    }

    /**
     * Render a twig template.
     *
     * @param string              $name       The template name.
     * @param array<string,mixed> $parameters The parameters.
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
     * @param string              $name       The template name.
     * @param array<string,mixed> $parameters The parameters.
     *
     * @throws InvalidArgumentException When an unsupported template name is given.
     */
    private function renderContaoTemplate(string $name, array $parameters): string
    {
        [$scope, $templateName] = $this->extractScopeAndTemplateName($name);

        return match ($scope) {
            'fe' => $this->templateFactory->createFrontendTemplate($templateName, $parameters)->parse(),
            'be' => $this->templateFactory->createBackendTemplate($templateName, $parameters)->parse(),
            default => throw new InvalidArgumentException(sprintf('Template scope "%s" is not supported', $scope)),
        };
    }

    /**
     * Extract the scope and template name from the whole template reference.
     *
     * @param string $name The template reference.
     *
     * @return array{string,string}
     *
     * @throws InvalidArgumentException When an unsupported template name is given.
     */
    private function extractScopeAndTemplateName(string $name): array
    {
        if (! preg_match('/^(toolkit:)?(be|fe):([^.]+)(\.html5)?$/', $name, $matches)) {
            throw new InvalidArgumentException(sprintf('Template name "%s" is not supported', $name));
        }

        return [$matches[2], $matches[3]];
    }
}
