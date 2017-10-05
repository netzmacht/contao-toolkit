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
     * Construct.
     *
     * @param TemplateFactory $templateFactory Template factory.
     * @param Translator      $translator      Translator.
     * @param Input           $input           Input.
     * @param bool            $replaceHex      If true no '#' prefix char is generated.
     * @param string          $template        Template name.
     */
    public function __construct(
        TemplateFactory $templateFactory,
        Translator $translator,
        $input,
        $replaceHex = false,
        $template = null
    ) {
        parent::__construct($templateFactory, $translator, $input, $template);

        $this->replaceHex = $replaceHex;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(string $fieldName): string
    {
        $template = $this->createTemplate();
        $template
            ->set('title', $this->translator->trans('MSC.colorpicker', [], 'contao_default'))
            ->set('field', $fieldName)
            ->set('icon', 'pickcolor.gif')
            ->set('replaceHex', $this->replaceHex);

        return $template->parse();
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke($dataContainer): string
    {
        return $this->generate($dataContainer->field);
    }
}
