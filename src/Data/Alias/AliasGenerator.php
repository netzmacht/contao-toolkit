<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database\Result;
use Contao\Model;

/**
 * Alias generator interface.
 */
interface AliasGenerator
{
    /**
     * Generate the alias.
     *
     * @param Result|Model $result The database result.
     * @param mixed        $value  The current value.
     */
    public function generate($result, $value = null): ?string;
}
