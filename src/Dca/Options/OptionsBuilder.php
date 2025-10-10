<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Options;

use Contao\Database\Result;
use Contao\Model\Collection;

use function array_merge;
use function is_callable;
use function str_repeat;

/**
 * Class OptionsBuilder is designed to transfer data to the requested format for options.
 */
final class OptionsBuilder
{
    /**
     * The options.
     */
    private Options $options;

    /**
     * Get Options builder for collection.
     *
     * @param Collection           $collection  Model collection.
     * @param callable|string|null $labelColumn Label column or callback.
     * @param string               $valueColumn Value column.
     *
     * @return OptionsBuilder
     */
    public static function fromCollection(
        Collection|null $collection = null,
        callable|string|null $labelColumn = null,
        string $valueColumn = 'id',
    ): self {
        if ($collection === null) {
            return new self(new ArrayListOptions([], $labelColumn, $valueColumn));
        }

        $options = new CollectionOptions($collection, $labelColumn, $valueColumn);

        return new self($options);
    }

    /**
     * Get Options builder for collection.
     *
     * @param Result|null          $result      Database result.
     * @param callable|string|null $labelColumn Label column or callback.
     * @param string               $valueColumn Value column.
     *
     * @return OptionsBuilder
     */
    public static function fromResult(
        Result|null $result = null,
        callable|string|null $labelColumn = null,
        string $valueColumn = 'id',
    ): self {
        if (! $result) {
            return self::fromArrayList([], $labelColumn, $valueColumn);
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        return self::fromArrayList($result->fetchAllAssoc(), $labelColumn, $valueColumn);
    }

    /**
     * Create options from array list.
     *
     * It expects an array which is a list of associative arrays where the value column is part of the associative
     * array and has to be extracted.
     *
     * @param list<array<string,mixed>> $data     Raw data list.
     * @param callable|string|null      $labelKey Label key or callback.
     * @param string                    $valueKey Value key.
     *
     * @return OptionsBuilder
     */
    public static function fromArrayList(
        array $data,
        callable|string|null $labelKey = null,
        string $valueKey = 'id',
    ): self {
        $options = new ArrayListOptions($data, $labelKey, $valueKey);

        return new self($options);
    }

    /**
     * Construct.
     *
     * @param Options $options The options.
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    /**
     * Group options by a specific column.
     *
     * @param string        $column   Column name.
     * @param callable|null $callback Optional callback.
     *
     * @return $this
     *
     * @psalm-suppress PossiblyNullArrayOffset
     */
    public function groupBy(string $column, callable|null $callback = null): self
    {
        $options = [];

        foreach ($this->options as $key => $value) {
            $row   = $this->options->row();
            $group = $this->groupValue($row[$column], $callback, $row);

            $options[$group][$key] = $value;
        }

        $this->options = new ArrayOptions($options);

        return $this;
    }

    /**
     * Get options as tree.
     *
     * @param string $parent   Column which stores parent value.
     * @param string $indentBy Indent entry by this value.
     *
     * @return $this
     *
     * @psalm-suppress PossiblyNullArrayOffset
     */
    public function asTree(string $parent = 'pid', string $indentBy = '-- '): self
    {
        $options = [];
        $values  = [];

        foreach ($this->options as $key => $value) {
            /** @psalm-suppress PossiblyNullArgument */
            $pid = $this->options[$key][$parent];

            /** @psalm-suppress PossiblyNullArgument */
            $values[$pid][$key] = array_merge($this->options[$key], ['__label__' => $value]);
        }

        $this->buildTree($values, $options, 0, $indentBy);

        $this->options = new ArrayOptions($options);

        return $this;
    }

    /**
     * Get the build options.
     *
     * @return array<string,string|array<string,string>>
     */
    public function getOptions(): array
    {
        return $this->options->getArrayCopy();
    }

    /**
     * Get the group value.
     *
     * @param mixed               $value    Raw group value.
     * @param callable|null       $callback Optional callback.
     * @param array<string,mixed> $row      Current data row.
     */
    private function groupValue(mixed $value, callable|null $callback, array $row): mixed
    {
        if (is_callable($callback)) {
            return $callback($value, $row);
        }

        return $value;
    }

    /**
     * Build options tree.
     *
     * @param array<int,array<int,mixed>> $values   The values.
     * @param array<int|string, string>   $options  The created options.
     * @param int                         $index    The current index.
     * @param string                      $indentBy The indent characters.
     * @param int                         $depth    The current depth.
     *
     * @return array<int|string, string>
     */
    private function buildTree(array &$values, array &$options, int $index, string $indentBy, int $depth = 0): array
    {
        /** @psalm-suppress RiskyTruthyFalsyComparison */
        if (empty($values[$index])) {
            return $options;
        }

        foreach ($values[$index] as $key => $value) {
            $options[$key] = str_repeat($indentBy, $depth) . ' ' . $value['__label__'];
            $this->buildTree($values, $options, $key, $indentBy, $depth + 1);
        }

        return $options;
    }
}
