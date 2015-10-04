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
use Netzmacht\Contao\Toolkit\View\Wizard\PagePicker;

/**
 * Feature PagePickerWizard adds page picker support the the callbacks class.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback\Feature
 */
trait PagePickerCallback
{
    /**
     * Page picker instance.
     *
     * @var PagePicker
     */
    protected $pagePicker;

    /**
     * Custom page picker template.
     *
     * @var null
     */
    protected $pagePickerTemplate = null;

    /**
     * Get the page picker.
     *
     * @return PagePicker
     */
    protected function getPagePicker()
    {
        if ($this->pagePicker === null) {
            $translator = $this->getServiceContainer()->getTranslator();
            $input      = $this->getServiceContainer()->getInput();

            $this->pagePicker = new PagePicker($translator, $input, $this->pagePickerTemplate);
        }

        return $this->pagePicker;
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
        return $this->getPagePicker()->generate(
            $dataContainer->table,
            $dataContainer->field,
            $dataContainer->id,
            $dataContainer->value
        );
    }
}
