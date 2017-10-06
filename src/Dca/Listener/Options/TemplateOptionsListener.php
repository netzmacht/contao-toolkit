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

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Options;

use Contao\Controller;
use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Manager;

/**
 * Class TemplateOptionsListener
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Listener\Options
 */
final class TemplateOptionsListener
{
    /**
     * Data container manager.
     *
     * @var Manager
     */
    private $dcaManager;

    /**
     * TemplateOptionsListener constructor.
     *
     * @param Manager $dcaManager Data container manager.
     */
    public function __construct(Manager $dcaManager)
    {
        $this->dcaManager = $dcaManager;
    }

    /**
     * Handle the options callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    public function handleOptionsCallback($dataContainer): array
    {
        $config    = $this->getConfig($dataContainer);
        $templates = Controller::getTemplateGroup($config['prefix']);

        if (empty($config['exclude'])) {
            return $templates;
        }

        return array_diff($templates, $config['exclude']);
    }

    /**
     * Get the callback config.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     */
    private function getConfig($dataContainer): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);
        $config     = array_merge(
            [
                'prefix' => '',
                'exclude' => null
            ],
            (array) $definition->get(['fields', $dataContainer->field, 'toolkit', 'template_options'])
        );

        return $config;
    }
}
