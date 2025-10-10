<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Button;

use Contao\Backend;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Monolog\SystemLogger;
use Contao\DataContainer;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Netzmacht\Contao\Toolkit\Data\Updater\Updater;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Exception\AccessDenied;
use RuntimeException;

use function array_merge;
use function preg_match;
use function preg_replace;
use function sprintf;

/**
 * StateButtonCallback creates the state toggle button known in Contao.
 */
final class StateButtonCallbackListener
{
    /**
     * The input.
     *
     * @var Adapter<Input>
     */
    private Adapter $input;

    /**
     * Data row updater.
     */
    private Updater $updater;

    /**
     * Data container manager.
     */
    private DcaManager $dcaManager;

    /**
     * Contao backend adapter.
     *
     * @var Adapter<Backend>
     */
    private Adapter $backend;

    /**
     * @param Adapter<Backend> $backend    Contao backend adapter.
     * @param Adapter<Input>   $input      Request Input.
     * @param Updater          $updater    Data record updater.
     * @param DcaManager       $dcaManager Data container manager.
     */
    public function __construct(
        Adapter $backend,
        Adapter $input,
        Updater $updater,
        DcaManager $dcaManager,
        private SystemLogger|null $logger = null,
    ) {
        $this->input      = $input;
        $this->updater    = $updater;
        $this->dcaManager = $dcaManager;
        $this->backend    = $backend;
    }

    /**
     * Invoke the callback.
     *
     * @param array<string,mixed>        $row               Current data row.
     * @param string|null                $href              Button link.
     * @param string|null                $label             Button label.
     * @param string|null                $title             Button title.
     * @param string|null                $icon              Enabled button icon.
     * @param string|null                $attributes        Html attributes as string.
     * @param string                     $tableName         Table name.
     * @param array<int,string|int>|null $rootIds           Root ids.
     * @param array<int,string|int>|null $childRecordIds    Child record ids.
     * @param bool                       $circularReference Circular reference flag.
     * @param string|null                $previous          Previous button name.
     * @param string|null                $next              Next button name.
     * @param DataContainer              $dataContainer     Data container driver.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function onButtonCallback(
        array $row,
        string|null $href,
        string|null $label,
        string|null $title,
        string|null $icon,
        string|null $attributes,
        string $tableName,
        array|null $rootIds,
        array|null $childRecordIds,
        bool $circularReference,
        string|null $previous,
        string|null $next,
        DataContainer $dataContainer,
    ): string {
        $name   = $this->getOperationName((string) $attributes);
        $config = $this->getConfig($dataContainer, $name);

        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if ($this->input->get('tid')) {
            try {
                /** @psalm-suppress RiskyCast */
                $this->updater->update(
                    $dataContainer->table,
                    (int) $this->input->get('tid'),
                    [$config['stateColumn'] => ((int) $this->input->get('state') === 1)],
                    $dataContainer,
                );

                $this->backend->redirect($this->backend->getReferer());
            } catch (AccessDenied $e) {
                $this->logger?->error($e->getMessage());
                $this->backend->redirect('contao?act=error');
            }
        }

        if (! $this->updater->hasUserAccess($dataContainer->table, $config['stateColumn'])) {
            return '';
        }

        $disabled = ! $row[$config['stateColumn']] || ($config['inverse'] && $row[$config['stateColumn']]);
        $href   ??= '';
        $href    .= '&amp;id=';

        /** @psalm-suppress PossiblyInvalidCast */
        $href .= (string) $this->input->get('id');
        $href .= '&amp;tid=';
        $href .= $row['id'] . '&amp;state=' . ($disabled ? '1' : '');

        if ($disabled) {
            $icon = $this->disableIcon((string) $icon, (string) $config['disabledIcon']);
        }

        $imageAttributes = sprintf('data-state="%s"', $disabled ? '' : '1');

        return sprintf(
            '<a href="%s" title="%s"%s>%s</a> ',
            $this->backend->addToUrl($href),
            StringUtil::specialchars((string) $title),
            (string) $attributes,
            Image::getHtml((string) $icon, (string) $label, $imageAttributes),
        );
    }

    /**
     * Disable the icon.
     *
     * @param string $icon    The enabled icon.
     * @param string $default The default icon.
     */
    private function disableIcon(string $icon, string $default = ''): string
    {
        if ($default) {
            return $default;
        }

        if (preg_match('/^visible\.(gif|png|svg|jpg)$/', $icon, $matches)) {
            return 'invisible.' . $matches[1];
        }

        return (string) preg_replace('/\.([^\.]*)$/', '_.$1', $icon);
    }

    /**
     * Get callback config.
     *
     * @param DataContainer $dataContainer Data container driver.
     * @param string        $operationName Operation name.
     *
     * @return array<string,mixed>
     */
    private function getConfig(DataContainer $dataContainer, string $operationName): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);
        $config     = [
            'disabledIcon' => null,
            'stateColumn'  => null,
            'inverse'      => false,
        ];

        return array_merge(
            $config,
            (array) $definition->get(['list', 'operations', $operationName, 'toolkit', 'state_button']),
        );
    }

    /**
     * Extract the operation name from the attributes.
     *
     * @param string $attributes Attributes section.
     *
     * @throws RuntimeException When no data-operation is set in the attributes.
     */
    private function getOperationName(string $attributes): string
    {
        if (preg_match('/data-operation="([^"]*)"/', $attributes, $matches)) {
            return $matches[1];
        }

        throw new RuntimeException('No data-operation attribute set to detect operation name.');
    }
}
