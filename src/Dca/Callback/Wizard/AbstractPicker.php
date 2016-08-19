<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Wizard;

use Input;
use ContaoCommunityAlliance\Translator\TranslatorInterface as Translator;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;

/**
 * AbstractPicker is the base class for a picker wizard.
 *
 * @package Netzmacht\Contao\Toolkit\View\Wizard
 */
abstract class AbstractPicker extends AbstractWizard
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $template = 'be_wizard_picker';

    /**
     * Request Input.
     *
     * @var Input
     */
    protected $input;

    /**
     * PagePickerCallback constructor.
     *
     * @param TemplateFactory $templateFactory Template factory.
     * @param Translator      $translator      Translator.
     * @param Input           $input           Input.
     * @param string          $template        Template name.
     */
    public function __construct(TemplateFactory $templateFactory, Translator $translator, Input $input, $template = null)
    {
        parent::__construct($templateFactory, $translator, $template);

        $this->input = $input;
    }
}
