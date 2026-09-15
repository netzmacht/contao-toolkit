<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\DelegatingTemplateRenderer;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

final class DelegatingTemplateRendererSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    public function let(TemplateFactory $templateFactory): void
    {
        $this->beConstructedWith($templateFactory);
    }

    public function it_is_initializable(): void
    {
        $this->shouldBeAnInstanceOf(DelegatingTemplateRenderer::class);
    }

    public function it_is_a_template_renderer(): void
    {
        $this->shouldBeAnInstanceOf(TemplateRenderer::class);
    }

    public function it_renders_contao_backend_template(
        TemplateFactory $templateFactory,
        Template $template,
    ): void {
        $templateFactory->createBackendTemplate('foo', [])->shouldBeCalled()->willReturn($template);
        $template->parse()->willReturn('foo_html');
        $this->render('be:foo')->shouldReturn('foo_html');
    }

    public function it_renders_toolkit_backend_template(
        TemplateFactory $templateFactory,
        Template $template,
    ): void {
        $templateFactory->createBackendTemplate('foo', [])->shouldBeCalled()->willReturn($template);
        $template->parse()->willReturn('foo_html');
        $this->render('toolkit:be:foo')->shouldReturn('foo_html');
    }

    public function it_renders_contao_frontend_template(
        TemplateFactory $templateFactory,
        Template $template,
    ): void {
        $templateFactory->createFrontendTemplate('foo', [])->shouldBeCalled()->willReturn($template);
        $template->parse()->willReturn('foo_html');
        $this->render('fe:foo')->shouldReturn('foo_html');
    }

    public function it_renders_toolkit_frontend_template(
        TemplateFactory $templateFactory,
        Template $template,
    ): void {
        $templateFactory->createFrontendTemplate('foo', [])->shouldBeCalled()->willReturn($template);
        $template->parse()->willReturn('foo_html');
        $this->render('toolkit:fe:foo')->shouldReturn('foo_html');
    }

    public function it_throws_on_invalid_template_name(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('render', ['sc:foo']);
        $this->shouldThrow(InvalidArgumentException::class)->during('render', ['fe:foo.html']);
        $this->shouldThrow(InvalidArgumentException::class)->during('render', ['contao:fe:foo.html5']);
    }

    public function it_throws_if_twig_is_not_available(): void
    {
        $this->shouldThrow(RuntimeException::class)->during('render', ['foo.html.twig']);
    }

    public function it_triggers_a_deprecation_warning_when_rendering_a_contao_template(
        TemplateFactory $templateFactory,
        Template $template,
    ): void {
        $templateFactory->createBackendTemplate('foo', [])->willReturn($template);
        $template->parse()->willReturn('foo_html');

        $messages = $this->captureDeprecations(function (): void {
            $this->render('be:foo');
        });

        $this->assertDeprecationTriggered($messages, 'Rendering legacy Contao templates via "be:foo"');
    }

    public function it_does_not_trigger_a_deprecation_warning_when_rendering_a_twig_template(
        TemplateFactory $templateFactory,
    ): void {
        $templateFactory->createFrontendTemplate()->shouldNotBeCalled();

        // A missing twig environment still throws RuntimeException, but no deprecation is
        // triggered for the twig branch itself.
        $messages = $this->captureDeprecations(function (): void {
            try {
                $this->render('foo.html.twig');
            } catch (RuntimeException) {
                // Expected: no twig environment configured in this spec's let().
            }
        });

        if ($messages !== []) {
            throw new \RuntimeException('Did not expect a deprecation warning for a twig template.');
        }
    }
}
