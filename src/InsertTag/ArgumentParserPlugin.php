<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\InsertTag;

/**
 * The argument parser plugin parses the arguments by a default schema.
 *
 * Following parsing strategy is used:
 * - Splits query by '::' into arguments
 * - Checks if any argument contains an url style query (foo?bar=baz)
 */
trait ArgumentParserPlugin
{
    /**
     * Argument parser.
     */
    private ArgumentParser $argumentParser;

    /** {@inheritDoc} */
    protected function parseArguments(string $query): array
    {
        if ($this->argumentParser === null) {
            $this->argumentParser = $this->createArgumentParser();
        }

        return $this->argumentParser->parse($query);
    }

    /**
     * Create the argument parser.
     */
    abstract protected function createArgumentParser(): ArgumentParser;
}
