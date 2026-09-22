<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\ColorPickerListener;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColorPickerListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    public function let(TemplateRenderer $templateRenderer, TranslatorInterface $translator, DcaManager $dcaManager): void
    {
        $this->beConstructedWith($templateRenderer, $translator, $dcaManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ColorPickerListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'ColorPickerListener is deprecated');
    }

    public function it_still_renders_with_the_new_twig_default_template(
        TemplateRenderer $templateRenderer,
        TranslatorInterface $translator,
        DcaManager $dcaManager,
    ): void {
        $dca = [];

        $translator->trans('MSC.colorpicker', [], 'contao_default')->willReturn('Pick a color');
        $dcaManager->getDefinition('tl_example')->willReturn(new Definition('tl_example', $dca));

        $templateRenderer
            ->render('@NetzmachtContaoToolkit/backend/wizard_color_picker.html.twig', Argument::type('array'))
            ->willReturn('<svg></svg>');

        $this->generate('tl_example', 'color')->shouldReturn('<svg></svg>');
    }
}
