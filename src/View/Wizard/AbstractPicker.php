<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\View\Wizard;

use Contao\Input;
use ContaoCommunityAlliance\Translator\TranslatorInterface;

/**
 * AbstractPicker is the base class for a picker wizard.
 *
 * @package Netzmacht\Contao\Toolkit\View\Wizard
 */
abstract class AbstractPicker
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $template = 'be_wizard_picker';

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Request Input.
     *
     * @var Input
     */
    protected $input;

    /**
     * PagePickerCallback constructor.
     *
     * @param TranslatorInterface $translator Translator.
     * @param Input               $input      Input.
     * @param string              $template   Template name.
     */
    public function __construct(TranslatorInterface $translator, Input $input, $template = null)
    {
        $this->translator = $translator;
        $this->input      = $input;

        if ($template) {
            $this->template = $template;
        }
    }

    /**
     * Generate the wizard.
     *
     * @param string $tableName Table name.
     * @param string $fieldName Field name.
     * @param int    $rowId     Row id
     * @param mixed  $value     Field value.
     *
     * @return string
     */
    abstract public function generate($tableName, $fieldName, $rowId, $value);
}
