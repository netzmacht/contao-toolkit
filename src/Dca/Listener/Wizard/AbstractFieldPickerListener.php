<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Listener\Wizard;

use Contao\DataContainer;
use Override;

abstract class AbstractFieldPickerListener extends AbstractPickerListener
{
    /**
     * Generate the picker.
     *
     * @param string $tableName Table name.
     * @param string $fieldName Field name.
     * @param int    $rowId     Row id.
     * @param mixed  $value     Field value.
     */
    abstract public function generate(
        string $tableName,
        string $fieldName,
        int $rowId,
        mixed $value,
    ): string;

    /** {@inheritDoc} */
    #[Override]
    public function onWizardCallback(DataContainer $dataContainer): string
    {
        return $this->generate(
            $dataContainer->table,
            $dataContainer->field,
            (int) $dataContainer->id,
            (string) $dataContainer->value,
        );
    }
}
