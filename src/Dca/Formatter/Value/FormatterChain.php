<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Assertion\Assertion;

/**
 * Set of multiple formatter.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class FormatterChain implements ValueFormatter
{
    /**
     * List of value formatter.
     *
     * @var ValueFormatter[]
     */
    private $formatter = array();

    /**
     * FormatterChain constructor.
     *
     * @param ValueFormatter[]|array $formatter Value formatter.
     */
    public function __construct(array $formatter)
    {
        Assertion::allImplementsInterface($formatter, ValueFormatter::class);

        $this->formatter = $formatter;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        foreach ($this->formatter as $formatter) {
            if ($formatter->accepts($fieldName, $fieldDefinition)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        foreach ($this->formatter as $formatter) {
            if ($formatter->accepts($fieldName, $fieldDefinition)) {
                return $formatter->format($value, $fieldName, $fieldDefinition, $context);
            }
        }

        return $value;
    }
}
