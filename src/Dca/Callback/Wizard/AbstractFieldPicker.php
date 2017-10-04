<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
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
