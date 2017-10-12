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

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * TemplateFactory creates a template with some predefined helpers.
 */
final class ToolkitTemplateFactory implements TemplateFactory
{
    /**
     * Event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * TemplateFactory constructor.
     *
     * @param EventDispatcher $eventDispatcher Event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function createFrontendTemplate(
        string $name,
        array $data = null,
        string $contentType = 'text/html'
    ): Template {
        $helpers  = $this->getTemplateHelpers($name, $contentType);
        $template = new FrontendTemplate($name, $helpers, $contentType);

        if ($data) {
            $template->setData($data);
        }

        return $template;
    }

    /**
     * {@inheritdoc}
     */
    public function createBackendTemplate(
        string $name,
        array $data = null,
        string $contentType = 'text/html'
    ): Template {
        $helpers  = $this->getTemplateHelpers($name, $contentType);
        $template = new BackendTemplate($name, $helpers, $contentType);

        if ($data) {
            $template->setData($data);
        }

        return $template;
    }

    /**
     * Get template helpers for an template.
     *
     * @param string $name        Template name.
     * @param string $contentType Template content type.
     *
     * @return array
     */
    private function getTemplateHelpers(string $name, string $contentType): array
    {
        $event = new GetTemplateHelpersEvent($name, $contentType);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->getHelpers();
    }
}
