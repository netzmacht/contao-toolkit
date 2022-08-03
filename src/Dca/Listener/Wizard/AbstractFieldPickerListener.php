<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

abstract class AbstractFieldPickerListener extends AbstractPickerListener
{
    /**
     * Generate the picker.
     *
     * @param string       $tableName Table name.
     * @param string       $fieldName Field name.
     * @param int          $rowId     Row id.
     * @param string|mixed $value     Field value.
     */
    abstract public function generate(
        string $tableName,
        string $fieldName,
        int $rowId,
        $value
    ): string;

    /**
     * {@inheritDoc}
     */
    public function onWizardCallback($dataContainer): string
    {
        return $this->generate(
            $dataContainer->table,
            $dataContainer->field,
            (int) $dataContainer->id,
            (string) $dataContainer->value
        );
    }
}
