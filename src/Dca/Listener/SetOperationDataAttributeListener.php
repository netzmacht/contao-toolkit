<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Dca\Manager;
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
     * @var Manager
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
     * @param Manager             $dcaManager Data container manager.
     * @param RequestScopeMatcher $scopeMatcher
     */
    public function __construct(Manager $dcaManager, RequestScopeMatcher $scopeMatcher)
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
        if ($this->scopeMatcher->isInstallRequest()) {
            return;
        }

        $definition = $this->dcaManager->getDefinition($dataContainerName);
        $buttons    = (array) $definition->get(['list', 'operations']);

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
