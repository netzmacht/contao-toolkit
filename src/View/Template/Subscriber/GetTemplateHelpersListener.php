<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template\Subscriber;

use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

/**
 * Class GetTemplateHelpersListener registers the default supported template helpers for all templates.
 */
final class GetTemplateHelpersListener
{
    /**
     * Assets manager.
     *
     * @var AssetsManager
     */
    private $assetsManager;

    /**
     * Translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * @param AssetsManager $assetsManager Assets manager.
     * @param Translator    $translator    Translator.
     */
    public function __construct(AssetsManager $assetsManager, Translator $translator)
    {
        $this->assetsManager = $assetsManager;
        $this->translator    = $translator;
    }

    /**
     * Handle the event.
     *
     * @param GetTemplateHelpersEvent $event Event.
     */
    public function handle(GetTemplateHelpersEvent $event): void
    {
        $event
            ->addHelper('assets', $this->assetsManager)
            ->addHelper('translator', $this->translator);
    }
}
