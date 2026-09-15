<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\Wizard\FilePickerListener;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FilePickerListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    /** @param Adapter<Input> $input */
    public function let(
        TemplateRenderer $templateRenderer,
        TranslatorInterface $translator,
        DcaManager $dcaManager,
        Adapter $input,
        RouterInterface $router,
    ): void {
        $this->beConstructedWith($templateRenderer, $translator, $dcaManager, $input, $router);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FilePickerListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'FilePickerListener is deprecated');
    }
}
