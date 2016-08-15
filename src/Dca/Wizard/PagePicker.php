<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Wizard;

use Netzmacht\Contao\Toolkit\View\Template\BackendTemplate;

/**
 * Class PagePickerCallback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
class PagePicker extends AbstractPicker
{
    /**
     * {@inheritDoc}
     */
    public function generate($tableName, $fieldName, $rowId, $value)
    {
        $url = sprintf(
            'contao/page.php?do=%s&amp;table=%s&amp;field=%s&amp;value=%',
            $this->input->get('do'),
            $tableName,
            $fieldName,
            str_replace(
                array('{{link_url::', '}}'),
                '',
                $value
            )
        );

        $cssId   = $fieldName . (($this->input->get('act') === 'editAll') ? '_' . $rowId : '');
        $jsTitle = specialchars(
            str_replace('\'', '\\\'', $this->translator->translate('MOD.page.0', 'modules'))
        );

        $template = new BackendTemplate($this->template);
        $template
            ->set('url', $url)
            ->set('title', $this->translator->translate('MSC.pagepicker'))
            ->set('jsTitle', $jsTitle)
            ->set('field', $fieldName)
            ->set('icon', 'pickpage.gif')
            ->set('id', $cssId);

        return $template->parse();
    }
}
