<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Button;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Controller;
use Contao\Image;
use Contao\Input;
use Contao\System;
use Netzmacht\Contao\Toolkit\Data\Updater\Updater;
use Netzmacht\Contao\Toolkit\Data\Exception\AccessDenied;

/**
 * StateButtonCallback creates the state toggle button known in Contao.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Button\Callback
 */
final class StateButtonCallback
{
    /**
     * The input.
     *
     * @var Input
     */
    private $input;

    /**
     * Data container name.
     *
     * @var string
     */
    private $tableName;

    /**
     * State column.
     *
     * @var string
     */
    private $stateColumn;

    /**
     * Disabled icon.
     *
     * @var string
     */
    private $disabledIcon;

    /**
     * If true state value is handled inverse.
     *
     * @var bool
     */
    private $inverse;

    /**
     * Data row updater.
     *
     * @var Updater
     */
    private $updater;

    /**
     * StateButtonCallback constructor.
     *
     * @param Input       $input        Request Input.
     * @param Updater     $updater      Data record updater.
     * @param string      $tableName    Data container name.
     * @param string      $stateColumn  Column name of the state value.
     * @param string|null $disabledIcon Disabled icon.
     * @param bool        $inverse      If true state value is handled inverse.
     */
    public function __construct(
        Input $input,
        Updater $updater,
        $tableName,
        $stateColumn,
        $disabledIcon = null,
        $inverse = false
    ) {
        $this->input        = $input;
        $this->updater      = $updater;
        $this->tableName    = $tableName;
        $this->stateColumn  = $stateColumn;
        $this->disabledIcon = $disabledIcon;
        $this->inverse      = $inverse;
    }

    /**
     * Invoke the callback.
     *
     * @param array         $row               Current data row.
     * @param string        $href              Button link.
     * @param string        $label             Button label.
     * @param string        $title             Button title.
     * @param string        $icon              Enabled button icon.
     * @param string        $attributes        Html attributes as string.
     * @param string        $tableName         Table name.
     * @param array         $rootIds           Root ids.
     * @param array         $childRecordIds    Child record ids.
     * @param bool          $circularReference Circular reference flag.
     * @param string        $previous          Previous button name.
     * @param string        $next              Next button name.
     * @param DataContainer $dataContainer     Data container driver.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __invoke(
        $row,
        $href,
        $label,
        $title,
        $icon,
        $attributes,
        $tableName,
        $rootIds,
        $childRecordIds,
        $circularReference,
        $previous,
        $next,
        $dataContainer
    ) {
        if ($this->input->get('tid')) {
            try {
                $this->updater->update(
                    $this->tableName,
                    $this->input->get('tid'),
                    [$this->stateColumn => ($this->input->get('state') == 1)],
                    $dataContainer
                );

                Controller::redirect(Controller::getReferer());
            } catch (AccessDenied $e) {
                System::log($e->getMessage(), __METHOD__, TL_ERROR);
                Controller::redirect('contao/main.php?act=error');
            }
        }

        if (!$this->updater->hasUserAccess($this->tableName, $this->stateColumn)) {
            return '';
        }

        $href .= '&amp;id='.$this->input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

        if (!$row[$this->stateColumn] || ($this->inverse && $row[$this->stateColumn])) {
            $icon = $this->disableIcon($icon);
        }

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            Backend::addToUrl($href),
            specialchars($title),
            $attributes,
            Image::getHtml($icon, $label)
        );
    }

    /**
     * Disable the icon.
     *
     * @param string $icon The enabled icon.
     *
     * @return string
     */
    private function disableIcon($icon)
    {
        if ($this->disabledIcon) {
            return $this->disabledIcon;
        }

        if ($icon === 'visible.gif') {
            return 'invisible.gif';
        }

        return preg_replace('\.([^\.]*)$', '._$1', $icon);
    }
}
