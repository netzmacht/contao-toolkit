<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Save;

use Netzmacht\Contao\Toolkit\Data\Alias\Generator;

/**
 * Class GenerateAliasCallback is designed to create an alias of a column.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
class GenerateAliasCallback
{
    /**
     * The alias generator.
     *
     * @var Generator
     */
    private $generator;

    /**
     * Construct.
     *
     * @param Generator $generator The alias generator.
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Generate the alias value.
     *
     * @param mixed          $value         The current value.
     * @param \DataContainer $dataContainer The data container driver.
     *
     * @return mixed|null|string
     */
    public function __invoke($value, $dataContainer)
    {
        return $this->generator->generate($dataContainer->activeRecord, $value);
    }
}
