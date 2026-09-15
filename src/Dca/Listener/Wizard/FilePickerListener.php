<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function sprintf;
use function str_replace;
use function trigger_deprecation;

/**
 * FilePicker wizard.
 *
 * @deprecated Use the native `eval => ['dcaPicker' => [...]]` field eval
 *             (Contao\Backend::getDcaPickerWizard()) instead. Will be removed in 5.0.
 */
final class FilePickerListener extends AbstractFieldPickerListener
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
        private RouterInterface $router,
        string $template = '',
    ) {
        parent::__construct($templateRenderer, $translator, $dcaManager, $template);

        $this->input = $input;

        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'FilePickerListener is deprecated. Use the native "dcaPicker" field eval instead.',
        );
    }

    /** {@inheritDoc} */
    #[Override]
    public function generate(string $tableName, string $fieldName, int $rowId, mixed $value = null): string
    {
        $url = $this->router->generate('contao_backend_picker', [
            'context' => 'file',
            'extras'  => [
                'fieldType' => 'radio',
                'filesOnly' => true,
                'source'    => sprintf('%s.%s', $tableName, $rowId),
            ],
            'value'   => $value,
            'popup'   => 1,
        ]);

        $cssId   = $fieldName . ($this->input->get('act') === 'editAll' ? '_' . $rowId : '');
        $jsTitle = StringUtil::specialchars(
            str_replace('\'', '\\\'', $this->translator->trans('MOD.files.0', [], 'contao_modules')),
        );

        $parameters = [
            'url'     => $url,
            'title'   => $this->translator->trans('MSC.filepicker', [], 'contao_default'),
            'jsTitle' => $jsTitle,
            'field'   => $fieldName,
            'icon'    => 'pickfile.svg',
            'id'      => $cssId,
        ];

        return $this->render($this->template, $parameters);
    }
}
