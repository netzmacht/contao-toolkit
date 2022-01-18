<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use RuntimeException;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

use function preg_match;
use function sprintf;

class ToolkitTemplateNameParser implements TemplateNameParserInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws RuntimeException If template name could not be parsed.
     */
    public function parse($name)
    {
        if ($name instanceof TemplateReferenceInterface) {
            $name = (string) $name;
        }

        if (preg_match('/^([^\:]{1,})\:([^\:]{1,})\:(.{1,})\.([^\.\:]{1,})\:?([^\:]*)$/', $name, $matches)) {
            $this->guardSupportedEngine($matches[1]);
            $this->guardSupportedScope($matches[2]);

            $contentType = $matches[5] ?: 'text/html';

            return new TemplateReference($matches[3], $matches[4], $matches[2], $contentType, $matches[1]);
        }

        if (preg_match('/^(be|fe):([^:]+)$/', $name, $matches)) {
            return new TemplateReference($matches[2], 'html5', $matches[1], 'text/html');
        }

        throw new RuntimeException(
            sprintf('Could not parse template name "%s". Expected format is "scope:name.format"', $name)
        );
    }

    /**
     * Guard that template scope is supported.
     *
     * @param string $scope Given scope.
     *
     * @throws RuntimeException When template scope is not supported.
     */
    private function guardSupportedScope(string $scope): void
    {
        if (
            $scope !== TemplateReference::SCOPE_FRONTEND
            && $scope !== TemplateReference::SCOPE_BACKEND
        ) {
            throw new RuntimeException(
                sprintf(
                    'Template scope "%s" is not supported. Has to be contao_frontend or contao_backend.',
                    $scope
                )
            );
        }
    }

    /**
     * Guard that template engine is supported.
     *
     * @param string $engine Given engine.
     *
     * @throws RuntimeException When template engine is not supported.
     */
    private function guardSupportedEngine(string $engine): void
    {
        if ($engine !== TemplateReference::ENGINE) {
            throw new RuntimeException(
                sprintf(
                    'Template engine "%s" is not supported. Has to be toolkit.',
                    $engine
                )
            );
        }
    }
}
