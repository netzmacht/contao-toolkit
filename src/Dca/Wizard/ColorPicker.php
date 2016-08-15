<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Wizard;

use Contao\Input;
use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Contao\Toolkit\View\Template\BackendTemplate;

/**
 * Class ColorPicker.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Wizard
 */
class ColorPicker extends AbstractPicker
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $template = 'be_wizard_color_picker';

    /**
     * If true no '#' prefix char is generated.
     *
     * @var bool
     */
    private $replaceHex;

    /**
     * Construct.
     *
     * @param TranslatorInterface $translator Translator.
     * @param Input               $input      Input.
     * @param bool                $replaceHex If true no '#' prefix char is generated.
     * @param string              $template   Template name.
     */
    public function __construct(TranslatorInterface $translator, Input $input, $replaceHex = false, $template = null)
    {
        parent::__construct($translator, $input, $template);

        $this->replaceHex = $replaceHex;
    }

    /**
     * {@inheritDoc}
     */
    public function generate($fieldName)
    {
        $template = new BackendTemplate($this->template);
        $template
            ->set('title', $this->translator->translate('MSC.colorpicker'))
            ->set('field', $fieldName)
            ->set('version', COLORPICKER)
            ->set('icon', 'pickcolor.gif')
            ->set('replaceHex', $this->replaceHex);

        return $template->parse();
    }
}
