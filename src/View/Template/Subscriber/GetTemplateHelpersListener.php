<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template\Subscriber;

use Netzmacht\Contao\Toolkit\View\Assets\AssetsManager;
use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class GetTemplateHelpersListener registers the default supported template helpers for all templates.
 *
 * @package Netzmacht\Contao\Toolkit\View\Template\Subscriber
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
     * GetTemplateHelpersListener constructor.
     *
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
     *
     * @return void
     */
    public function handle(GetTemplateHelpersEvent $event): void
    {
        $event
            ->addHelper('assets', $this->assetsManager)
            ->addHelper('translator', $this->translator);
    }
}
