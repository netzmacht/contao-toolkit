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

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Wizard;

use Contao\Input;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use Symfony\Component\Translation\TranslatorInterface as Translator;

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
    public function __construct(
        TemplateFactory $templateFactory,
        Translator $translator,
        $input,
        $template = null
    ) {
        parent::__construct($templateFactory, $translator, $template);

        $this->input = $input;
    }
}
