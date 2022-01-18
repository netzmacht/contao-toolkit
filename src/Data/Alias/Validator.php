<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\Database\Result;
use Contao\Model;

/**
 * Interface Validator describes an alias validator.
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue.NullabilityTypeMissing
 */
interface Validator
{
    /**
     * Validate a value.
     *
     * @param Result|Model          $result  The database result.
     * @param mixed                 $value   Given value to validate.
     * @param list<string|int>|null $exclude Set of ids which should be ignored.
     */
    public function validate($result, $value, array $exclude = null): bool;
}
