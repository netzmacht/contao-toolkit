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

use Contao\StringUtil;

/**
 * Class ArgumentParser.
 *
 * @package Netzmacht\Contao\Toolkit\InsertTag
 */
final class ArgumentParser
{
    /**
     * List of parsing callbacks.
     *
     * @var array
     */
    private $parsers = [];

    /**
     * Mark if arguments are already splitted.
     *
     * @var bool
     */
    private $splitted = false;

    /**
     * Create the argument parser.
     *
     * @return static
     */
    public static function create(): self
    {
        return new static();
    }

    /**
     * Register a split callback.
     *
     * @param string     $separator Separator string.
     * @param array|null $names     Optional list for named attributes.
     * @param int|null   $limit     Optional split limit.
     *
     * @return ArgumentParser
     *
     * @throws \RuntimeException When another split callback is registered before.
     */
    public function splitBy(string $separator = '::', array $names = null, int $limit = null): self
    {
        $this->guardNoSplitCallbackRegistered($separator);

        $this->parsers[] = function ($query) use ($separator, $names, $limit) {
            return $this->handleSplitBy($query, $separator, $names, $limit);
        };

        return $this;
    }

    /**
     * Add a query parser.
     *
     * @param array|null $argumentIndexes If given only these arguments got parsed.
     *
     * @return ArgumentParser
     */
    public function parseQuery(array $argumentIndexes = null): self
    {
        $this->parsers[] = function ($arguments) use ($argumentIndexes) {
            return $this->handleParseQuery((array) $arguments, $argumentIndexes);
        };

        return $this;
    }

    /**
     * Parse the argument query.
     *
     * @param string $query Argument query.
     *
     * @return array
     */
    public function parse(string $query): array
    {
        $parsed = $query;

        foreach ($this->parsers as $parser) {
            $parsed = $parser($parsed);
        }

        if (is_array($parsed)) {
            return $parsed;
        }

        return [];
    }

    /**
     * Handle the split by.
     *
     * @param string     $query     The given query.
     * @param string     $separator Separator string.
     * @param array|null $names     Optional list for named attributes.
     * @param int|null   $limit     Optional split limit.
     *
     * @return array
     */
    private function handleSplitBy(string $query, string $separator, array $names = null, int $limit = null)
    {
        if ($limit === null) {
            $values = explode($separator, $query);
        } else {
            $values = explode($separator, $query, $limit);
        }

        if ($names === null) {
            return $values;
        }

        $named = [];

        foreach ($names as $index => $name) {
            if (array_key_exists($index, $values)) {
                $named[$name] = $values[$index];
            } else {
                $named[$name] = null;
            }
        }

        return $named;
    }

    /**
     * Handle the query parsing.
     *
     * @param array      $arguments       Insert tag arguments.
     * @param array|null $argumentIndexes If given only these arguments got parsed.
     *
     * @return array
     */
    private function handleParseQuery(array $arguments, array $argumentIndexes = null): array
    {
        if ($argumentIndexes === null) {
            foreach ($arguments as $index => $argument) {
                $arguments[$index] = $this->parseArgumentQuery($argument);
            }

            return $arguments;
        }

        foreach ($argumentIndexes as $index) {
            if (array_key_exists($index, $arguments)) {
                $arguments[$index] = $this->parseArgumentQuery($arguments[$index]);
            }
        }

        return $arguments;
    }

    /**
     * Parse an argument query.
     *
     * Expect argument value is foo?bar=baz&foobar=1
     *
     * @param string $argument Argument query.
     *
     * @return array
     */
    private function parseArgumentQuery(string $argument): array
    {
        $parts = explode('?', $argument, 2);

        if (!isset($parts[1])) {
            return [
                'value'   => $parts[0],
                'options' => [],
            ];
        }

        $parts[1] = str_replace('+', '&', $parts[1]);
        $parts[1] = StringUtil::decodeEntities($parts[1]);
        parse_str($parts[1], $options);

        return [
            'value'   => $parts[0],
            'options' => $options,
        ];
    }

    /**
     * Guard that no split callback is registered before.
     *
     * @param string $separator Separator character.
     *
     * @return void
     *
     * @throws \RuntimeException When another split callback is registered before.
     */
    protected function guardNoSplitCallbackRegistered(string $separator)
    {
        if ($this->splitted) {
            throw new \RuntimeException(
                sprintf(
                    'Could not register split by "%s" parser. There is a already previous split callback registered',
                    $separator
                )
            );
        }
    }
}
