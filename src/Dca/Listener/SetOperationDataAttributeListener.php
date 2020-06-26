<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;

/**
 * This listener defines an data-operation attribute for each operation which has a toolkit config section.
 *
 * The button callback doesn't have an identifier to the button which the button is rendering. To overcome this
 * limitation an data attribute is set.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Listener
 */
class SetOperationDataAttributeListener
{
    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * Request scope matcher.
     *
     * @var RequestScopeMatcher
     */
    private $scopeMatcher;

    /**
     * SetOperationDataAttributeListener constructor.
     *
     * @param DcaManager          $dcaManager   Data container manager.
     * @param RequestScopeMatcher $scopeMatcher The scope matcher.
     */
    public function __construct(DcaManager $dcaManager, RequestScopeMatcher $scopeMatcher)
    {
        $this->dcaManager   = $dcaManager;
        $this->scopeMatcher = $scopeMatcher;
    }

    /**
     * Listen to the on load data container callback.
     *
     * @param string $dataContainerName Data container name.
     *
     * @return void
     */
    public function onLoadDataContainer(string $dataContainerName)
    {
        if (!$this->scopeMatcher->isContaoRequest() || $this->scopeMatcher->isInstallRequest()) {
            return;
        }

        try {
            $definition = $this->dcaManager->getDefinition($dataContainerName);
        } catch (AssertionFailed $e) {
            // No valid dca config found. Just ignore the data container.
            return;
        }

        $buttons = (array) $definition->get(['list', 'operations']);

        foreach ($buttons as $name => $config) {
            if (!isset($config['toolkit'])) {
                continue;
            }

            if (!isset($config['attributes'])) {
                $config['attributes'] = '';
            }

            $attributes = trim($config['attributes'] . ' data-operation="' . $name . '"');
            $definition->set(['list', 'operations', $name, 'attributes'], $attributes);
        }
    }
}
