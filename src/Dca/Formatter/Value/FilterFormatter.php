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

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Webmozart\Assert\Assert;

/**
 * FilterFormatter applies formatter used as filters to an value.
 *
 * The difference between the FormatterChain and the FilterFormatter is that the filter formatter will apply all
 * rules and modifies the given value.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class FilterFormatter implements ValueFormatter
{
    /**
     * List of filters.
     *
     * @var ValueFormatter[]
     */
    private $filters = array();

    /**
     * Construct.
     *
     * @param ValueFormatter[]|array $filters List of filters.
     */
    public function __construct(array $filters)
    {
        Assert::allIsInstanceOf($filters, 'Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');

        $this->filters = $filters;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        foreach ($this->filters as $filter) {
            if ($filter->accepts($fieldName, $fieldDefinition)) {
                $value = $filter->format($value, $fieldName, $fieldDefinition, $context);
            }
        }

        return $value;
    }
}
