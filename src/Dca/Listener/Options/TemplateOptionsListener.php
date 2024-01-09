<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Options;

use Contao\Controller;
use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;

use function array_diff;
use function array_map;
use function array_merge;
use function array_values;

final class TemplateOptionsListener
{
    /**
     * Data container manager.
     */
    private DcaManager $dcaManager;

    /** @param DcaManager $dcaManager Data container manager. */
    public function __construct(DcaManager $dcaManager)
    {
        $this->dcaManager = $dcaManager;
    }

    /**
     * Handle the option callback.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return list<string>
     *
     * @psalm-suppress MoreSpecificReturnType
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function onOptionsCallback(DataContainer $dataContainer): array
    {
        $config    = $this->getConfig($dataContainer);
        $templates = Controller::getTemplateGroup($config['prefix']);

        if (empty($config['exclude'])) {
            return $templates;
        }

        return array_values(array_map('\strval', array_diff($templates, $config['exclude'])));
    }

    /**
     * Get the callback config.
     *
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return array<string,mixed>
     */
    private function getConfig(DataContainer $dataContainer): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);

        return array_merge(
            [
                'prefix' => '',
                'exclude' => null,
            ],
            (array) $definition->get(['fields', $dataContainer->field, 'toolkit', 'template_options']),
        );
    }
}
