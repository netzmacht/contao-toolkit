<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template;

use Override;
use Twig\Environment;

/** Class DelegatingTemplateRenderer renders Twig templates. */
final class DelegatingTemplateRenderer implements TemplateRenderer
{
    public function __construct(private readonly Environment $twig)
    {
    }

    /** {@inheritDoc} */
    #[Override]
    public function render(string $name, array $parameters = []): string
    {
        return $this->twig->render($name, $parameters);
    }
}
