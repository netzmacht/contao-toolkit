<?php

/**
 * @package    toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Template;

use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * TemplateFactory creates a template with some predefined helpers.
 */
final class TemplateFactory
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
     * Create a frontend template.
     *
     * @param string     $name        Template name.
     * @param array|null $data        Template data.
     * @param string     $contentType Content type.
     *
     * @return Template
     */
    public function createFrontendTemplate($name, array $data = null, $contentType = 'text/html')
    {
        $helpers  = $this->getTemplateHelpers($name, $contentType);
        $template = new FrontendTemplate($name, $helpers, $contentType);

        if ($data) {
            $template->setData($data);
        }

        return $template;
    }

    /**
     * Create a backend template.
     *
     * @param string     $name        Template name.
     * @param array|null $data        Template data.
     * @param string     $contentType Content type.
     *
     * @return Template
     */
    public function createBackendTemplate($name, array $data = null, $contentType = 'text/html')
    {
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
    private function getTemplateHelpers($name, $contentType)
    {
        $event = new GetTemplateHelpersEvent($name, $contentType);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        return $event->getHelpers();
    }
}
