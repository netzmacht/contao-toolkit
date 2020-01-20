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

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use const E_USER_DEPRECATED;

/**
 * Class AbstractFieldPicker.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback\Wizard
 */
abstract class AbstractFieldPickerListener extends AbstractPickerListener
{
    /**
     * Generate the picker.
     *
     * @param string $tableName Table name.
     * @param string $fieldName Field name.
     * @param int    $rowId     Row id.
     * @param string $value     Field value.
     *
     * @return string
     */
    abstract public function generate(string $tableName, string $fieldName, int $rowId, $value): string;

    /**
     * {@inheritDoc}
     */
    public function onWizardCallback($dataContainer): string
    {
        return $this->generate(
            $dataContainer->table,
            $dataContainer->field,
            (int) $dataContainer->id,
            $dataContainer->value
        );
    }

    /**
     * {@inheritDoc}
     */
    public function handleWizardCallback($dataContainer): string
    {
        // @codingStandardsIgnoreStart
        @trigger_error(
            sprintf(
                '%1$s::handleWizardCallback is deprecated and will be removed in Version 4.0.0. '
                . 'Use %1$s::onWizardCallback instead.',
                static::class
            ),
            E_USER_DEPRECATED
        );
        // @codingStandardsIgnoreEnd

        return $this->onWizardCallback($dataContainer);
    }
}
