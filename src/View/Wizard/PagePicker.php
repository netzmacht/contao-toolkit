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
use Netzmacht\Contao\Toolkit\View\BackendTemplate;

/**
 * Class PagePickerCallback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
class PagePicker
{
    /**
     * Template name.
     *
     * @var string
     */
    private $template = 'be_callback_page_picker';

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var Input
     */
    private $input;

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
     * Invoke the callback.
     *
     * @param string $tableName Table name.
     * @param string $fieldName Field name.
     * @param int    $rowId     Row id
     * @param mixed  $value     Field value.
     *
     * @return string
     */
    public function generate($tableName, $fieldName, $rowId, $value)
    {
        $url = sprintf(
            'contao/page.php?do=%s&amp;table=%s&amp;field=%s&amp;value=%',
            $this->input->get('do'),
            $tableName,
            $fieldName,
            str_replace(
                array('{{link_url::', '}}'),
                '',
                $value
            )
        );

        $cssId   = $fieldName . (($this->input->get('act') === 'editAll') ? '_' . $rowId : '');
        $jsTitle = specialchars(
            str_replace('\'', '\\\'', $this->translator->translate('MOD.page.0', 'modules'))
        );

        $template = new BackendTemplate($this->template);
        $template
            ->set('url', $url)
            ->set('title', $this->translator->translate('MSC.pagepicker'))
            ->set('jsTitle', $jsTitle)
            ->set('field', $fieldName)
            ->set('id', $cssId);

        return $template->parse();
    }
}
