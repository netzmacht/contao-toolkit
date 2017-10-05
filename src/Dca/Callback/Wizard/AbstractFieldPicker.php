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

declare(strict_types=1);

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
     * @param int    $rowId     Row id.
     * @param string $value     Field value.
     *
     * @return string
     */
    abstract public function generate(string $tableName, string $fieldName, int $rowId, string $value): string;

    /**
     * {@inheritDoc}
     */
    public function __invoke($dataContainer): string
    {
        return $this->generate(
            $dataContainer->table,
            $dataContainer->field,
            (int) $dataContainer->id,
            $dataContainer->value
        );
    }
}
