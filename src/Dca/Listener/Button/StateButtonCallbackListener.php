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

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Button;

use Contao\Backend;
use Contao\DataContainer;
use Contao\Controller;
use Contao\Image;
use Contao\Input;
use Contao\System;
use Netzmacht\Contao\Toolkit\Data\Updater\Updater;
use Netzmacht\Contao\Toolkit\Data\Exception\AccessDenied;
use Netzmacht\Contao\Toolkit\Dca\Manager;

/**
 * StateButtonCallback creates the state toggle button known in Contao.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Button\Callback
 */
final class StateButtonCallbackListener
{
    /**
     * The input.
     *
     * @var Input
     */
    private $input;

    /**
     * Data row updater.
     *
     * @var Updater
     */
    private $updater;

    /**
     * Data container manager.
     *
     * @var Manager
     */
    private $dcaManager;

    /**
     * StateButtonCallback constructor.
     *
     * @param Input   $input      Request Input.
     * @param Updater $updater    Data record updater.
     * @param Manager $dcaManager Data container manager.
     */
    public function __construct(
        $input,
        Updater $updater,
        Manager $dcaManager
    ) {
        $this->input      = $input;
        $this->updater    = $updater;
        $this->dcaManager = $dcaManager;
    }

    /**
     * Invoke the callback.
     *
     * @param array         $row               Current data row.
     * @param string|null   $href              Button link.
     * @param string|null   $label             Button label.
     * @param string|null   $title             Button title.
     * @param string|null   $icon              Enabled button icon.
     * @param string|null   $attributes        Html attributes as string.
     * @param string        $tableName         Table name.
     * @param array|null    $rootIds           Root ids.
     * @param array|null    $childRecordIds    Child record ids.
     * @param bool          $circularReference Circular reference flag.
     * @param string|null   $previous          Previous button name.
     * @param string|null   $next              Next button name.
     * @param DataContainer $dataContainer     Data container driver.
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function handleButtonCallback(
        array $row,
        ?string $href,
        ?string $label,
        ?string $title,
        ?string $icon,
        ?string $attributes,
        string $tableName,
        ?array $rootIds,
        ?array $childRecordIds,
        bool $circularReference,
        ?string $previous,
        ?string $next,
        $dataContainer
    ) {
        $name   = $this->getOperationName($attributes);
        $config = $this->getConfig($dataContainer, $name);

        if ($this->input->get('tid')) {
            try {
                $this->updater->update(
                    $dataContainer->table,
                    $this->input->get('tid'),
                    [$config['stateColumn'] => ($this->input->get('state') == 1)],
                    $dataContainer
                );

                Controller::redirect(Controller::getReferer());
            } catch (AccessDenied $e) {
                System::log($e->getMessage(), __METHOD__, TL_ERROR);
                Controller::redirect('contao/main.php?act=error');
            }
        }

        if (!$this->updater->hasUserAccess($dataContainer->table, $config['stateColumn'])) {
            return '';
        }

        $href .= '&amp;id='.$this->input->get('id').'&amp;tid='.$row['id'].'&amp;state='.$row[''];

        if (!$row[$config['stateColumn']] || ($config['inverse'] && $row[$config['stateColumn']])) {
            $icon = $this->disableIcon($icon, $config['disabledIcon']);
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
     * @param string      $icon    The enabled icon.
     * @param null|string $default The default icon.
     *
     * @return string
     */
    private function disableIcon(string $icon, ?string $default = null): string
    {
        if ($default) {
            return $default;
        }

        if (preg_match('/^visible\.(gif|png|svg|jpg)$/', $icon, $matches)) {
            return 'invisible.' . $matches[1];
        }

        return preg_replace('/\.([^\.]*)$/', '_.$1', $icon);
    }

    /**
     * Get callback config.
     *
     * @param DataContainer $dataContainer Data container driver.
     * @param string        $operationName Operation name.
     *
     * @return array
     */
    private function getConfig($dataContainer, string $operationName): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);
        $config     = [
            'disabledIcon' => null,
            'stateColumn'  => null,
            'inverse'      => false
        ];

        return array_merge(
            $config,
            (array) $definition->get(['list', 'operations', $operationName, 'toolkit', 'state_button'])
        );
    }

    /**
     * Extract the operation name from the attributes.
     *
     * @param string $attributes Attributes section.
     *
     * @return string
     *
     * @throws \RuntimeException When no data-operation is set in the attributes.
     */
    private function getOperationName($attributes): string
    {
        if (preg_match('/data-operation="([^"]*)"/', $attributes, $matches)) {
            return $matches[1];
        }

        throw new \RuntimeException('No data-operation attribute set to detect operation name.');
    }
}
