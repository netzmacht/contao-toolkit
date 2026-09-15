<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Save;

use Contao\CoreBundle\Slug\Slug;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Data\Alias\SlugAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;

use function array_merge;

final class SlugAliasListener
{
    public function __construct(
        private readonly Slug $slug,
        private readonly Connection $connection,
        private readonly DcaManager $dcaManager,
    ) {
    }

    public function onSaveCallback(mixed $value, DataContainer $dataContainer): string
    {
        return $this->getGenerator($dataContainer)->generate($dataContainer->activeRecord, $value);
    }

    private function getGenerator(DataContainer $dataContainer): SlugAliasGenerator
    {
        $config = $this->getConfig($dataContainer);

        $validator = new UniqueDatabaseValueValidator(
            $this->connection,
            $dataContainer->table,
            $dataContainer->field,
            $config['unique_key_fields'],
            $config['allow_empty'],
        );

        return new SlugAliasGenerator($this->slug, $validator, $dataContainer->table, $config['fields']);
    }

    /** @return array{fields: list<string>, unique_key_fields: list<string>, allow_empty: bool} */
    private function getConfig(DataContainer $dataContainer): array
    {
        $definition = $this->dcaManager->getDefinition($dataContainer->table);

        return array_merge(
            [
                'fields' => ['id'],
                'unique_key_fields' => [],
                'allow_empty' => false,
            ],
            (array) $definition->get(['fields', $dataContainer->field, 'toolkit', 'alias_generator']),
        );
    }
}
