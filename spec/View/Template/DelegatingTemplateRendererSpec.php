<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template\DelegatingTemplateRenderer;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use Twig\Environment;

final class DelegatingTemplateRendererSpec extends ObjectBehavior
{
    public function let(Environment $twig): void
    {
        $this->beConstructedWith($twig);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(DelegatingTemplateRenderer::class);
    }

    public function it_is_a_template_renderer(): void
    {
        $this->shouldBeAnInstanceOf(TemplateRenderer::class);
    }

    public function it_renders_a_twig_template(Environment $twig): void
    {
        $twig->render('foo.html.twig', ['bar' => 'baz'])->willReturn('foo_html');
        $this->render('foo.html.twig', ['bar' => 'baz'])->shouldReturn('foo_html');
    }
}
