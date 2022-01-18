<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database\Result;
use Contao\Model;

/**
 * Alias generator interface.
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
 */
interface AliasGenerator
{
    /**
     * Generate the alias.
     *
     * @param Result|Model $result The database result.
     * @param mixed        $value  The current value.
     *
     * @return string|null
     */
    public function generate($result, $value = null);
}
