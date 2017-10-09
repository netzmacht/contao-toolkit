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

/**
 * Class ColorPicker.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Wizard
 */
final class ColorPickerListener extends AbstractPickerListener
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $template = 'contao_backend:be_wizard_color_picker.html5';

    /**
     * Generate the color picker.
     *
     * @param string $dataContainerName Data container name.
     * @param string $fieldName         Field name.
     *
     * @return string
     */
    public function generate(string $dataContainerName, string $fieldName): string
    {
        $config          = $this->getConfig($dataContainerName, $fieldName);
        $config['field'] = $fieldName;

        return $this->render($this->template, $config);
    }

    /**
     * {@inheritDoc}
     */
    public function handleWizardCallback($dataContainer): string
    {
        return $this->generate($dataContainer->table, $dataContainer->field);
    }

    /**
     * Get the picker configuration.
     *
     * @param string $dataContainerName Data container name.
     * @param string $fieldName         Field name.
     *
     * @return array
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
            (array) $definition->get(['fields', $fieldName, 'toolkit', 'color_picker'])
        );
    }
}
