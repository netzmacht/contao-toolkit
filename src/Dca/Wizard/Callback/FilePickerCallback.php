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

use DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Wizard\FilePicker;

/**
 * Feature FilePickerWizard adds file picker support the the callbacks class.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback\Feature
 */
trait FilePickerCallback
{
    /**
     * File picker instances.
     *
     * @var FilePicker[]
     */
    protected $filePicker = [];

    /**
     * Get the file picker.
     *
     * @param string $fieldName Field name.
     *
     * @return FilePicker
     */
    protected function getFilePicker($fieldName)
    {
        if (!isset($this->filePicker[$fieldName])) {
            $translator = $this->getServiceContainer()->getTranslator();
            $input      = $this->getServiceContainer()->getInput();
            $template   = $this->getDefinition()->get(['fields', $fieldName, 'toolkit', 'file_picker', 'template']);

            $this->filePicker[$fieldName] = new FilePicker($translator, $input, $template);
        }

        return $this->filePicker[$fieldName];
    }

    /**
     * Generate the page picker wizard.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string
     */
    public function generateFilePicker(DataContainer $dataContainer)
    {
        return $this->getFilePicker($dataContainer->field)->generate(
            $dataContainer->table,
            $dataContainer->field,
            $dataContainer->id,
            $dataContainer->value
        );
    }
}
