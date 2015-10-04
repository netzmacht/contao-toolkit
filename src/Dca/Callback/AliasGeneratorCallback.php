<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\ExistingAliasFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SlugifyFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Filter\SuffixFilter;
use Netzmacht\Contao\Toolkit\Data\Alias\Generator;

/**
 * Alias generator callback.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback\Feature
 */
trait AliasGeneratorCallback
{
    /**
     * The alias generator.
     *
     * @var Generator
     */
    protected $aliasGenerator;

    /**
     * Get the alias generator.
     *
     * @param string $column Alias column.
     *
     * @return Generator
     */
    protected function getAliasGenerator($column)
    {
        if ($this->aliasGenerator === null) {
            $fields  = (array) $this->getDefinition()->get(['fields', $column, 'alias_generator', 'fields'], 'id');
            $filters = [
                new ExistingAliasFilter(),
                new SlugifyFilter($fields),
                new SuffixFilter()
            ];

            $this->aliasGenerator = new Generator(
                $filters,
                $this->getServiceContainer()->getDatabaseConnection(),
                $this->name,
                $column
            );
        }

        return $this->aliasGenerator;
    }

    /**
     * Generate the alias.
     *
     * @param mixed         $value         Current value.
     * @param DataContainer $dataContainer Data container driver.
     *
     * @return mixed|null|string
     */
    public function generateAlias($value, $dataContainer)
    {
        return $this->getAliasGenerator($dataContainer->field)->generate($dataContainer->activeRecord, $value);
    }
}
