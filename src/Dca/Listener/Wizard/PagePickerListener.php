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

use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use Symfony\Component\Translation\TranslatorInterface as Translator;

/**
 * Class PagePickerCallback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
final class PagePickerListener extends AbstractFieldPickerListener
{
    /**
     * Request input.
     *
     * @var Adapter|Input
     */
    private $input;

    /**
     * PagePickerCallback constructor.
     *
     * @param TemplateFactory $templateFactory Template factory.
     * @param Translator      $translator      Translator.
     * @param Manager         $dcaManager      Data container manager.
     * @param Input|Adapter   $input           Request input.
     * @param string|null     $template        Template name.
     */
    public function __construct(
        TemplateFactory $templateFactory,
        Translator $translator,
        Manager $dcaManager,
        $input,
        $template = null
    ) {
        parent::__construct($templateFactory, $translator, $dcaManager, $template);

        $this->input = $input;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(string $tableName, string $fieldName, int $rowId, ?string $value = null): string
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
            str_replace('\'', '\\\'', $this->translator->trans('MOD.page.0', [], 'contao_modules'))
        );

        $template = $this->createTemplate();
        $template
            ->set('url', $url)
            ->set('title', $this->translator->trans('MSC.pagepicker', [], 'contao_default'))
            ->set('jsTitle', $jsTitle)
            ->set('field', $fieldName)
            ->set('icon', 'pickpage.gif')
            ->set('id', $cssId);

        return $template->parse();
    }
}
