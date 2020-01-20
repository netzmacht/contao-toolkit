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

namespace Netzmacht\Contao\Toolkit\Data\Updater;

/**
 * Update describes a handler class which updates an data container row.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Updater
 */
interface Updater
{
    /**
     * Toggle the state.
     *
     * @param string $dataContainerName Data container name.
     * @param int    $recordId          Data record id.
     * @param array  $data              Data of the row which should be changed.
     * @param mixed  $context           Context, usually the data container driver.
     *
     * @return mixed
     */
    public function update($dataContainerName, $recordId, array $data, $context);

    /**
     * Check if user has access to a data container field.
     *
     * @param string $dataContainerName Data container name.
     * @param string $columnName        Column name.
     *
     * @return bool
     */
    public function hasUserAccess($dataContainerName, $columnName);
}
