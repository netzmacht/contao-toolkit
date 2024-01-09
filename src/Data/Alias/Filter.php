<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

/**
 * Filter modifies a value for the alias generator.
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
     */
    public function initialize(): void;

    /**
     * Apply the filter.
     *
     * @param object      $model     The database result, usually a Contao\Database\Result or Contao\Model object.
     * @param string|null $value     Current value.
     * @param string      $separator Separator character between different alias tokens.
     */
    public function apply(object $model, string|null $value, string $separator): string|null;
}
