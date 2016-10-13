<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Template\Subscriber;

use Interop\Container\ContainerInterface as Container;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;
use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;

/**
 * Class GetTemplateHelpersListener registers the default supported template helpers for all templates.
 *
 * @package Netzmacht\Contao\Toolkit\View\Template\Subscriber
 */
class GetTemplateHelpersListener
{
    /**
     * Service container.
     *
     * @var Container
     */
    private $container;

    /**
     * GetTemplateHelpersListener constructor.
     *
     * @param Container $container Service container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Handle the event.
     *
     * @param GetTemplateHelpersEvent $event Event.
     *
     * @return void
     */
    public function handle(GetTemplateHelpersEvent $event)
    {
        $event
            ->addHelper('assets', $this->container->get(Services::ASSETS_MANAGER))
            ->addHelper('translator', $this->container->get(Services::TRANSLATOR));
    }
}
