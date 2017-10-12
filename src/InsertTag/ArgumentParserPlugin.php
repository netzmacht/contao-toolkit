<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2017 netzmacht David Molineus. All rights reserved.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-leaflet-maps/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\InsertTag;

/**
 * The argument parser plugin parses the arguments by a default schema.
 *
 * Following parsing strategy is used:
 * - Splits query by '::' into arguments
 * - Checks if any argument contains an url style query (foo?bar=baz)
 *
 * @package Netzmacht\Contao\Toolkit\InsertTag
 */
trait ArgumentParserPlugin
{
    /**
     * Argument parser.
     *
     * @var ArgumentParser
     */
    private $argumentParser;

    /**
     * {@inheritdoc}
     */
    protected function parseArguments(string $query): array
    {
        if ($this->argumentParser === null) {
            $this->argumentParser = $this->createArgumentParser();
        }

        return $this->argumentParser->parse($query);
    }

    /**
     * Create the argument parser.
     *
     * @return ArgumentParser
     */
    abstract protected function createArgumentParser(): ArgumentParser;
}
