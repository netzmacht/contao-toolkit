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

/**
 * TemplateFactory creates a template with some predefined helpers.
 */
class TemplateFactory
{
    /**
     * Template helper.
     *
     * @var callable[]
     */
    private $helpers;

    /**
     * TemplateFactory constructor.
     *
     * @param callable[] $helpers View helpers.
     */
    public function __construct($helpers)
    {
        $this->helpers = $helpers;
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
        $template = new FrontendTemplate($name, $this->helpers, $contentType);

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
        $template = new BackendTemplate($name, $this->helpers, $contentType);

        if ($data) {
            $template->setData($data);
        }

        return $template;
    }
}
