<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\View\Template\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class GetTemplateHelpersEvent is triggered when the helpers for a template are generated.
 */
final class GetTemplateHelpersEvent extends Event
{
    /**
     * The event name.
     */
    public const NAME = 'netzmacht.contao_toolkit.view.get_template_helpers';

    /**
     * Template name.
     */
    private string $templateName;

    /**
     * Content type.
     */
    private string $contentType;

    /**
     * List of helpers.
     *
     * @var array<string,object|callable>
     */
    private array $helpers = [];

    /**
     * @param string $templateName Template name.
     * @param string $contentType  Content type.
     */
    public function __construct(string $templateName, string $contentType = 'text/html')
    {
        $this->templateName = $templateName;
        $this->contentType  = $contentType;
    }

    /**
     * Get templateName.
     */
    public function getTemplateName(): string
    {
        return $this->templateName;
    }

    /**
     * Get contentType.
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Register a helper.
     *
     * @param string          $name   Name of the helper.
     * @param callable|object $helper Helper object.
     *
     * @return $this
     */
    public function addHelper(string $name, callable|object $helper): self
    {
        $this->helpers[$name] = $helper;

        return $this;
    }

    /**
     * Register a set of helpers.
     *
     * @param array<string,callable|object> $helpers Associate array with helper name as key and helper object as value.
     *
     * @return $this
     */
    public function addHelpers(array $helpers): self
    {
        foreach ($helpers as $name => $helper) {
            $this->addHelper($name, $helper);
        }

        return $this;
    }

    /**
     * Get helpers.
     *
     * @return array<string,object|callable>
     */
    public function getHelpers(): array
    {
        return $this->helpers;
    }
}
