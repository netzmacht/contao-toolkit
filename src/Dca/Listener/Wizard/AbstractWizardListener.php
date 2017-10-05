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

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * AbstractWizard is the base class for a wizard.
 *
 * @package Netzmacht\Contao\Toolkit\View\Wizard
 */
abstract class AbstractWizardListener
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
     * Data container manager.
     *
     * @var Manager
     */
    protected $dcaManager;

    /**
     * PagePickerCallback constructor.
     *
     * @param TemplateFactory $templateFactory Template factory.
     * @param Translator      $translator      Translator.
     * @param Manager         $dcaManager      Data container manager.
     * @param string|null     $template        Template name.
     */
    public function __construct(
        TemplateFactory $templateFactory,
        Translator $translator,
        Manager $dcaManager,
        ?string $template = null
    ) {
        $this->translator      = $translator;
        $this->templateFactory = $templateFactory;
        $this->dcaManager      = $dcaManager;

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
    protected function createTemplate(?string $name = null): Template
    {
        return $this->templateFactory->createBackendTemplate($name ?: $this->template);
    }

    /**
     * Get the data container definition.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return Definition
     */
    protected function getDefinition($dataContainer): Definition
    {
        return $this->dcaManager->getDefinition($dataContainer->table);
    }

    /**
     * Invoke by the callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string
     */
    abstract public function handleWizardCallback($dataContainer): string;
}
