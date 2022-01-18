<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database\Result;
use Contao\Model;

/**
 * Filter modifies a value for the alias generator.
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
 */
interface Filter
{
    /**
     * If true the filter can be applied until an unique value is generated.
     */
    public function repeatUntilValid(): bool;

    /**
     * If true no ongoing filters get applied.
     */
    public function breakIfValid(): bool;

    /**
     * Initialize the filter.
     *
     * @return void
     */
    public function initialize();

    /**
     * Apply the filter.
     *
     * @param Model|Result $model     Current model.
     * @param mixed        $value     Current value.
     * @param string       $separator Separator character between different alias tokens.
     *
     * @return string
     */
    public function apply($model, $value, string $separator);
}
