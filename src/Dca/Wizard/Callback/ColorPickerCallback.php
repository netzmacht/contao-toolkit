<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Wizard\Callback;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Wizard\ColorPicker;

/**
 * ColorPickerCallback adds color picker support the the callbacks class.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback\Feature
 */
trait ColorPickerCallback
{
    /**
     * Color picker instances.
     *
     * @var ColorPicker[]
     */
    private $colorPicker = [];

    /**
     * Get the color picker.
     *
     * @param string $fieldName Field name.
     *
     * @return ColorPicker
     */
    protected function getColorPicker($fieldName)
    {
        if (!isset($this->colorPicker[$fieldName])) {
            $translator = $this->getServiceContainer()->getTranslator();
            $input      = $this->getServiceContainer()->getInput();

            $definition = $this->getDefinition();
            $template   = $definition->get(['fields', $fieldName, 'toolkit', 'color_picker', 'template']);
            $replaceHex = !$definition->get(['fields', $fieldName, 'toolkit', 'color_picker', 'hex'], true);

            $this->colorPicker[$fieldName] = new ColorPicker($translator, $input, $replaceHex, $template);
        }

        return $this->colorPicker[$fieldName];
    }

    /**
     * Generate the color picker wizard.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string
     */
    public function generateColorPicker(DataContainer $dataContainer)
    {
        return $this->getColorPicker($dataContainer->field)->generate($dataContainer->field);
    }
}
