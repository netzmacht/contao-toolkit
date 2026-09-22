<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;

use function array_keys;
use function in_array;

/**
 * Auto-registers the remaining toolkit DCA field callbacks based on "fields.<field>.toolkit.<key>"
 * config presence, so consumers no longer have to wire options_callback/save_callback/wizard
 * manually in addition to the toolkit config.
 */
final class RegisterFieldCallbacksListener
{
    /**
     * Callback slots that hold a single [class, method] pair, never to be appended to.
     *
     * Every other slot is treated as a list slot (array of [class, method] pairs) that is safe
     * to append to. This mirrors Contao's own default behaviour, since toolkit config can wire
     * arbitrary DCA callback slots and we cannot know all of them upfront. Keep this list in
     * sync with Contao's canonical list, see
     * {@see \Contao\CoreBundle\EventListener\DataContainerCallbackListener::SINGLETONS}.
     */
    private const SINGLETON_SLOTS = [
        'button_callback',
        'child_record_callback',
        'default',
        'group_callback',
        'header_callback',
        'input_field_callback',
        'label_callback',
        'options_callback',
        'panel_callback',
        'paste_button_callback',
        'title_tag_callback',
        'url_callback',
    ];

    /** @param array<string,array{slot:string,service:string,method:string}> $callbacks */
    public function __construct(
        private readonly DcaManager $dcaManager,
        private readonly ScopeMatcher $scopeMatcher,
        private readonly array $callbacks,
    ) {
    }

    public function onLoadDataContainer(string $dataContainerName): void
    {
        if (! $this->scopeMatcher->isContaoRequest()) {
            return;
        }

        try {
            $definition = $this->dcaManager->getDefinition($dataContainerName);
        } catch (AssertionFailed) {
            // No valid dca config found. Just ignore the data container.
            return;
        }

        $fields = (array) $definition->get(['fields']);

        foreach ($fields as $field => $config) {
            $toolkitConfig = (array) ($config['toolkit'] ?? []);

            foreach (array_keys($toolkitConfig) as $key) {
                if (! isset($this->callbacks[$key])) {
                    continue;
                }

                $this->registerCallback($definition, (string) $field, $this->callbacks[$key]);
            }
        }
    }

    /** @param array{slot:string,service:string,method:string} $callback */
    private function registerCallback(Definition $definition, string $field, array $callback): void
    {
        $path  = ['fields', $field, $callback['slot']];
        $entry = [$callback['service'], $callback['method']];

        if (in_array($callback['slot'], self::SINGLETON_SLOTS, true)) {
            // Singleton slot (e.g. options_callback): never replace an already-set callback.
            if ($definition->has($path)) {
                return;
            }

            $definition->set($path, $entry);

            return;
        }

        $definition->modify($path, static function (mixed $current) use ($entry): array {
            $current = (array) $current;

            if (in_array($entry, $current, true)) {
                return $current;
            }

            return [...$current, $entry];
        });
    }
}
