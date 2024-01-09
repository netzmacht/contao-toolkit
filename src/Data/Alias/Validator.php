<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

/**
 * Interface Validator describes an alias validator.
 */
interface Validator
{
    /**
     * Validate a value.
     *
     * @param object                $result  The database result, usually a Contao\Database\Result or Contao\Model
     *                                       object.
     * @param mixed                 $value   Given value to validate.
     * @param list<string|int>|null $exclude Set of ids which should be ignored.
     */
    public function validate(object $result, mixed $value, array|null $exclude = null): bool;
}
