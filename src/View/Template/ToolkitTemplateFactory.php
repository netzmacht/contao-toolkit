<?php

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
     */
    private EventDispatcher $eventDispatcher;

    /** @param EventDispatcher $eventDispatcher Event dispatcher. */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritDoc}
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
     */
    public function createFrontendTemplate(
        string $name,
        array $data = null,
        string $contentType = 'text/html',
    ): Template {
        $helpers  = $this->getTemplateHelpers($name, $contentType);
        $template = new FrontendTemplate($name, $helpers, $contentType);

        if ($data) {
            $template->setData($data);
        }

        return $template;
    }

    /**
     * {@inheritDoc}
     *
     * @phpcs:disable SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
     */
    public function createBackendTemplate(
        string $name,
        array $data = null,
        string $contentType = 'text/html',
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
     * @return array<string,object|callable>
     */
    private function getTemplateHelpers(string $name, string $contentType): array
    {
        $event = new GetTemplateHelpersEvent($name, $contentType);
        $this->eventDispatcher->dispatch($event, $event::NAME);

        return $event->getHelpers();
    }
}
