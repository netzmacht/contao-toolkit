<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function sprintf;
use function str_replace;

final class PagePickerListener extends AbstractFieldPickerListener
{
    /**
     * Request input.
     *
     * @var Adapter<Input>
     */
    private Adapter $input;

    /**
     * @param TemplateRenderer $templateRenderer Template renderer.
     * @param Translator       $translator       Translator.
     * @param DcaManager       $dcaManager       Data container manager.
     * @param Adapter<Input>   $input            Request input.
     * @param string           $template         Template name.
     */
    public function __construct(
        TemplateRenderer $templateRenderer,
        Translator $translator,
        DcaManager $dcaManager,
        Adapter $input,
        string $template = ''
    ) {
        parent::__construct($templateRenderer, $translator, $dcaManager, $template);

        $this->input = $input;
    }

    /**
     * {@inheritDoc}
     */
    public function generate(string $tableName, string $fieldName, int $rowId, $value = null): string
    {
        $url = sprintf(
            'contao/page.php?do=%s&amp;table=%s&amp;field=%s&amp;value=%s',
            $this->input->get('do'),
            $tableName,
            $fieldName,
            str_replace(
                ['{{link_url::', '}}'],
                '',
                $value
            )
        );

        $cssId   = $fieldName . ($this->input->get('act') === 'editAll' ? '_' . $rowId : '');
        $jsTitle = StringUtil::specialchars(
            str_replace('\'', '\\\'', $this->translator->trans('MOD.page.0', [], 'contao_modules'))
        );

        $parameters = [
            'url' => $url,
            'title' => $this->translator->trans('MSC.pagepicker', [], 'contao_default'),
            'jsTitle' => $jsTitle,
            'field' => $fieldName,
            'icon' => 'pickpage.svg',
            'id' => $cssId,
        ];

        return $this->render($this->template, $parameters);
    }
}
