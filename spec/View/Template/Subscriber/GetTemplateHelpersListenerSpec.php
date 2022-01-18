<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\View\Template\Subscriber;

use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use PhpSpec\ObjectBehavior;
use Symfony\Contracts\Translation\TranslatorInterface;

use function expect;

class GetTemplateHelpersListenerSpec extends ObjectBehavior
{
    public function let(AssetsManager $assetsManager, TranslatorInterface $translator): void
    {
        $this->beConstructedWith($assetsManager, $translator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\View\Template\Subscriber\GetTemplateHelpersListener');
    }

    public function it_registers_default_helpers(): void
    {
        $event = new GetTemplateHelpersEvent('example');

        $this->handle($event);

        expect($event->getHelpers()['assets'])->shouldHaveType(AssetsManager::class);
        expect($event->getHelpers()['translator'])->shouldHaveType(TranslatorInterface::class);
    }
}
