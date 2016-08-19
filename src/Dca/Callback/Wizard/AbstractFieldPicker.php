<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Wizard;

/**
 * Class AbstractFieldPicker.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback\Wizard
 */
abstract class AbstractFieldPicker extends AbstractPicker
{
    /**
     * Generate the picker.
     *
     * @param string $tableName Table name.
     * @param string $fieldName Field name.
     * @param string $rowId     Row id.
     * @param string $value     Field value.
     *
     * @return mixed
     */
    abstract public function generate($tableName, $fieldName, $rowId, $value);

    /**
     * {@inheritDoc}
     */
    public function __invoke($dataContainer)
    {
        return $this->generate($dataContainer->table, $dataContainer->field, $dataContainer->id, $dataContainer->value);
    }
}
