<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

/**
 * Alias generator interface.
 */
interface AliasGenerator
{
    /**
     * Generate the alias.
     *
     * @param object     $result The database result, usually a Contao\Database\Result or Contao\Model object.
     * @param mixed|null $value  The current value.
     */
    public function generate(object $result, mixed $value = null): string|null;
}
