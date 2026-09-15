<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\View\Template\TemplateRenderer;
use Override;
use Symfony\Contracts\Translation\TranslatorInterface as Translator;

use function array_merge;
use function trigger_deprecation;

/**
 * @deprecated Use the native `eval => ['colorpicker' => true]` field eval instead. Will be removed in 5.0.
 */
final class ColorPickerListener extends AbstractPickerListener
{
    /**
     * Template name.
     */
    protected string $template = '@NetzmachtContaoToolkit/backend/wizard_color_picker.html.twig';

    public function __construct(TemplateRenderer $templateRenderer, Translator $translator, DcaManager $dcaManager)
    {
        parent::__construct($templateRenderer, $translator, $dcaManager);

        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'ColorPickerListener is deprecated. Use the native "colorpicker" field eval instead.',
        );
    }

    /**
     * Generate the color picker.
     *
     * @param string $dataContainerName Data container name.
     * @param string $fieldName         Field name.
     */
    public function generate(string $dataContainerName, string $fieldName): string
    {
        $config          = $this->getConfig($dataContainerName, $fieldName);
        $config['field'] = $fieldName;

        return $this->render($this->template, $config);
    }

    /** {@inheritDoc} */
    #[Override]
    public function onWizardCallback(DataContainer $dataContainer): string
    {
        return $this->generate($dataContainer->table, $dataContainer->field);
    }

    /**
     * Get the picker configuration.
     *
     * @param string $dataContainerName Data container name.
     * @param string $fieldName         Field name.
     *
     * @return array<string,mixed>
     */
    public function getConfig(string $dataContainerName, string $fieldName): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainerName);
        $config     = [
            'title'      => $this->translator->trans('MSC.colorpicker', [], 'contao_default'),
            'template'   => $this->template,
            'icon'       => 'pickcolor.svg',
            'replaceHex' => false,
        ];

        return array_merge(
            $config,
            (array) $definition->get(['fields', $fieldName, 'toolkit', 'color_picker']),
        );
    }
}
