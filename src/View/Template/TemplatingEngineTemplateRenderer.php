<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Symfony\Component\Templating\EngineInterface;

/**
 * This class is a wrapper for the deprecated templating engine implementing the TemplateRenderer interface
 *
 * @internal
 * @deprecated This class get removed in version 4.0.
 */
class TemplatingEngineTemplateRenderer implements TemplateRenderer
{
    /**
     * The template engine.
     *
     * @var EngineInterface
     */
    private $templatingEngine;

    /**
     * @param EngineInterface $templatingEngine The template engine.
     */
    public function __construct(EngineInterface $templatingEngine)
    {
        $this->templatingEngine = $templatingEngine;
    }

    /**
     * {@inheritDoc}
     */
    public function render(string $name, array $parameters = []): string
    {
        return $this->templatingEngine->render($name, $parameters);
    }
}
