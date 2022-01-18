<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Options;

use Contao\Controller;
use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;

use function array_diff;
use function array_merge;

use const E_USER_DEPRECATED;

final class TemplateOptionsListener
{
    /**
     * Data container manager.
     *
     * @var DcaManager
     */
    private $dcaManager;

    /**
     * @param DcaManager $dcaManager Data container manager.
     */
    public function __construct(DcaManager $dcaManager)
    {
        $this->dcaManager = $dcaManager;
    }

    /**
     * Handle the options callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return list<string>
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
     * @deprecated Deprecated and removed in Version 4.0.0. Use self::onOptionsCallback instead.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return list<string>
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
     * @return array<string,mixed>
     */
    private function getConfig($dataContainer): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);

        return array_merge(
            [
                'prefix' => '',
                'exclude' => null,
            ],
            (array) $definition->get(['fields', $dataContainer->field, 'toolkit', 'template_options'])
        );
    }
}
