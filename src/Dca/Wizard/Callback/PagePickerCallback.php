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
use Netzmacht\Contao\Toolkit\Dca\Wizard\PagePicker;

/**
 * Feature PagePickerWizard adds page picker support the the callbacks class.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback\Feature
 */
trait PagePickerCallback
{
    /**
     * Page picker instances.
     *
     * @var PagePicker[]
     */
    protected $pagePicker = [];

    /**
     * Get the page picker.
     *
     * @param string $fieldName Field name.
     *
     * @return PagePicker
     */
    protected function getPagePicker($fieldName)
    {
        if (!isset($this->pagePicker[$fieldName])) {
            $translator = $this->getServiceContainer()->getTranslator();
            $input      = $this->getServiceContainer()->getInput();
            $template   = $this->getDefinition()->get(['fields', $fieldName, 'toolkit', 'page_picker', 'template']);

            $this->pagePicker[$fieldName] = new PagePicker($translator, $input, $template);
        }

        return $this->pagePicker[$fieldName];
    }

    /**
     * Generate the page picker wizard.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string
     */
    public function generatePagePicker(DataContainer $dataContainer)
    {
        return $this->getPagePicker($dataContainer->field)->generate(
            $dataContainer->table,
            $dataContainer->field,
            $dataContainer->id,
            $dataContainer->value
        );
    }
}
