<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\View\Wizard\FilePicker;

/**
 * Feature FilePickerWizard adds file picker support the the callbacks class.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback\Feature
 */
trait FilePickerCallback
{
    /**
     * File picker instance.
     *
     * @var FilePicker
     */
    protected $filePicker;

    /**
     * Custom file picker template.
     *
     * @var null
     */
    protected $filePickerTemplate = null;

    /**
     * Get the file picker.
     *
     * @return FilePicker
     */
    protected function getFilePicker()
    {
        if ($this->filePicker === null) {
            $translator = $this->getServiceContainer()->getTranslator();
            $input      = $this->getServiceContainer()->getInput();

            $this->filePicker = new FilePicker($translator, $input, $this->filePickerTemplate);
        }

        return $this->filePicker;
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
        return $this->getFilePicker()->generate(
            $dataContainer->table,
            $dataContainer->field,
            $dataContainer->id,
            $dataContainer->value
        );
    }
}
