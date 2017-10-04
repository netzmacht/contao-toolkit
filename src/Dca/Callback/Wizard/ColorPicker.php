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

use Contao\Input;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class ColorPicker.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Wizard
 */
final class ColorPicker extends AbstractPicker
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
     * Color picker version.
     *
     * @var string
     */
    private $version;

    /**
     * Construct.
     *
     * @param TemplateFactory $templateFactory Template factory.
     * @param Translator      $translator      Translator.
     * @param Input           $input           Input.
     * @param string          $version         Colorpicker version.
     * @param bool            $replaceHex      If true no '#' prefix char is generated.
     * @param string          $template        Template name.
     */
    public function __construct(
        TemplateFactory $templateFactory,
        Translator $translator,
        Input $input,
        $version,
        $replaceHex = false,
        $template = null
    ) {
        parent::__construct($templateFactory, $translator, $input, $template);

        $this->replaceHex = $replaceHex;
        $this->version    = $version;
    }

    /**
     * {@inheritDoc}
     */
    public function generate($fieldName)
    {
        $template = $this->createTemplate();
        $template
            ->set('title', $this->translator->trans('MSC.colorpicker', [], 'contao_default'))
            ->set('field', $fieldName)
            ->set('version', $this->version)
            ->set('icon', 'pickcolor.gif')
            ->set('replaceHex', $this->replaceHex);

        return $template->parse();
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($dataContainer)
    {
        return $this->generate($dataContainer->field);
    }
}
