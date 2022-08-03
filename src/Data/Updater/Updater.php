<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Updater;

/**
 * Update describes a handler class which updates an data container row.
 */
interface Updater
{
    /**
     * Toggle the state.
     *
     * @param string              $dataContainerName Data container name.
     * @param string|int          $recordId          Data record id.
     * @param array<string,mixed> $data              Data of the row which should be changed.
     * @param mixed               $context           Context, usually the data container driver.
     *
     * @return mixed
     */
    public function update(string $dataContainerName, $recordId, array $data, $context);

    /**
     * Check if user has access to a data container field.
     *
     * @param string $dataContainerName Data container name.
     * @param string $columnName        Column name.
     */
    public function hasUserAccess(string $dataContainerName, string $columnName): bool;
}
