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
use Netzmacht\Contao\Toolkit\Dca\Wizard\PopupWizard;

/**
 * Class PopupWizardCallback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Wizard\Callback
 */
trait PopupWizardCallback
{
    /**
     * Popup wizard instances.
     *
     * @var array
     */
    protected $popupWizards = array();

    /**
     * Get the popup wizard.
     *
     * @param string $fieldName Field name.
     *
     * @return PopupWizard
     */
    protected function getPopupWizard($fieldName)
    {
        if (!isset($this->popupWizards[$fieldName])) {
            $translator   = $this->getServiceContainer()->getTranslator();
            $requestToken = \RequestToken::getInstance();
            $definition   = $this->getDefinition();

            $config   = $definition->get(['fields', $fieldName, 'toolkit', 'popup']);
            $always   = empty($config['always']) ? false : true;
            $template = empty($config['template']) ? null : $config['template'];
            $pattern  = empty($config['pattern']) ? null : $config['pattern'];
            $href     = empty($config['href']) ? '#' : $config['pattern'];
            $label    = empty($config['label']) ? $fieldName : $config['label'];
            $title    = empty($config['title']) ? $label : $config['title'];
            $icon     = empty($config['icon']) ? 'edit.gif' : $config['icon'];

            $wizard = new PopupWizard($translator, $requestToken, $href, $label, $title, $icon, $always, $template);

            if ($pattern) {
                $wizard->setLinkPattern($pattern);
            }

            $this->popupWizards[$fieldName] = $wizard;
        }

        return $this->popupWizards[$fieldName];
    }

    /**
     * Generate the popup wizard.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return string
     */
    public function generatePopupWizard($dataContainer)
    {
        return $this->getPopupWizard($dataContainer->field)->generate($dataContainer->value);
    }
}
