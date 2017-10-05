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

namespace Netzmacht\Contao\Toolkit\Dca\Callback\Save;

use Contao\DataContainer;
use Netzmacht\Contao\Toolkit\Data\Alias\AliasGenerator;
use Webmozart\Assert\Assert;

/**
 * Class GenerateAliasCallback is designed to create an alias of a column.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Callback
 */
final class GenerateAliasCallback
{
    /**
     * The alias generator.
     *
     * @var AliasGenerator
     */
    private $generator;

    /**
     * Construct.
     *
     * @param AliasGenerator $generator The alias generator.
     */
    public function __construct(AliasGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Generate the alias value.
     *
     * @param mixed         $value         The current value.
     * @param DataContainer $dataContainer The data container driver.
     *
     * @return mixed|null|string
     */
    public function __invoke($value, $dataContainer)
    {
        Assert::isInstanceOf($dataContainer, 'DataContainer');
        Assert::isInstanceOf($dataContainer->activeRecord, 'Database\Result');

        return $this->generator->generate($dataContainer->activeRecord, $value);
    }
}
