<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Wizard;

use DataContainer;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * AbstractWizard is the base class for a wizard.
 *
 * @package Netzmacht\Contao\Toolkit\View\Wizard
 */
abstract class AbstractWizard
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $template;

    /**
     * Translator.
     *
     * @var Translator
     */
    protected $translator;

    /**
     * Template factory.
     *
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * PagePickerCallback constructor.
     *
     * @param TemplateFactory $templateFactory Template factory.
     * @param Translator      $translator      Translator.
     * @param string          $template        Template name.
     */
    public function __construct(TemplateFactory $templateFactory, Translator $translator, $template = null)
    {
        $this->translator      = $translator;
        $this->templateFactory = $templateFactory;

        if ($template) {
            $this->template = $template;
        }
    }

    /**
     * Create a new template instance.
     *
     * @param string|null $name Custom template name. If null main wizard template is used.
     *
     * @return Template
     */
    protected function createTemplate($name = null)
    {
        return $this->templateFactory->createBackendTemplate($name ?: $this->template);
    }

    /**
     * Invoke by the callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string
     */
    abstract public function __invoke($dataContainer);
}
