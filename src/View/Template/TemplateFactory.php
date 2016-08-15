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

use ContaoCommunityAlliance\Translator\Contao\LangArrayTranslator;
use ContaoCommunityAlliance\Translator\TranslatorChain;
use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Contao\Toolkit\ServiceContainerTrait;
use Netzmacht\Contao\Toolkit\View\Template\BackendTemplate;
use Netzmacht\Contao\Toolkit\View\Template\FrontendTemplate;
use Netzmacht\Contao\Toolkit\View\Helper\ViewHelper;
use Netzmacht\Contao\Toolkit\View\Template;

/**
 * TemplateFactory creates a template with some predefined helpers.
 */
class TemplateFactory
{
    /**
     * Template helper.
     *
     * @var ViewHelper
     */
    private $helper;

    /**
     * TemplateFactory constructor.
     *
     * @param ViewHelper $helper
     */
    public function __construct(ViewHelper $helper)
    {
        $this->helper = $helper;
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
        $template = new FrontendTemplate($name, $this->helper, $contentType);

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
        $template = new BackendTemplate($name, $this->helper, $contentType);

        if ($data) {
            $template->setData($data);
        }

        return $template;
    }
}
