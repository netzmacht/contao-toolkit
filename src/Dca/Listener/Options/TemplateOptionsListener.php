<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Options;

use Contao\Controller;
use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use const E_USER_DEPRECATED;

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
    public function onOptionsCallback($dataContainer): array
    {
        $config    = $this->getConfig($dataContainer);
        $templates = Controller::getTemplateGroup($config['prefix']);

        if (empty($config['exclude'])) {
            return $templates;
        }

        return array_diff($templates, $config['exclude']);
    }

    /**
     * Handle the options callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array
     *
     * @deprecated Deprecated and removed in Version 4.0.0. Use self::onOptionsCallback instead.
     */
    public function handleOptionsCallback($dataContainer): array
    {
        // @codingStandardsIgnoreStart
        @trigger_error(
            sprintf(
                '%1$s::handleOptionsCallback is deprecated and will be removed in Version 4.0.0. '
                . 'Use %1$s::onOptionsCallback instead.',
                static::class
            ),
            E_USER_DEPRECATED
        );
        // @codingStandardsIgnoreEnd

        return $this->onOptionsCallback($dataContainer);
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
