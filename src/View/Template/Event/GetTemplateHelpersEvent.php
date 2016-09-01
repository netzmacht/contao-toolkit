<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Template\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class GetTemplateHelpersEvent is triggered when the helpers for a template are generated.
 *
 * @package Netzmacht\Contao\Toolkit\View\Template\Event
 */
final class GetTemplateHelpersEvent extends Event
{
    /**
     * The event name.
     */
    const NAME = 'toolkit.view.get-template-helpers';

    /**
     * Template name.
     *
     * @var string
     */
    private $templateName;

    /**
     * Content type.
     *
     * @var string
     */
    private $contentType;

    /**
     * List of helpers.
     *
     * @var array
     */
    private $helpers;

    /**
     * GetTemplateHelpersEvent constructor.
     *
     * @param string $templateName Template name.
     * @param string $contentType  Content type.
     */
    public function __construct($templateName, $contentType = 'text/html')
    {
        $this->templateName = $templateName;
        $this->contentType  = $contentType;
    }

    /**
     * Get templateName.
     *
     * @return string
     */
    public function getTemplateName()
    {
        return $this->templateName;
    }

    /**
     * Get contentType.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Register a helper.
     *
     * @param string $name   Name of the helper.
     * @param mixed  $helper Helper object.
     *
     * @return $this
     */
    public function addHelper($name, $helper)
    {
        $this->helpers[$name] = $helper;

        return $this;
    }

    /**
     * Register a set of helpers.
     *
     * @param array $helpers Associate array with helper name as key and helper object as value.
     *
     * @return $this
     */
    public function addHelpers(array $helpers)
    {
        foreach ($helpers as $name => $helper) {
            $this->addHelper($name, $helper);
        }

        return $this;
    }

    /**
     * Get helpers.
     *
     * @return array
     */
    public function getHelpers()
    {
        return $this->helpers;
    }
}
