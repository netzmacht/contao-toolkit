<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\StringUtil;

/**
 * Class HtmlValue.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class HtmlFormatter implements ValueFormatter
{
    /**
     * {@inheritDoc}
     */
    public function accepts(string $fieldName, array $fieldDefinition): bool
    {
        if (!empty($fieldDefinition['eval']['allowHtml'])) {
            return true;
        }

        if (!empty($fieldDefinition['eval']['preserveTags'])) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, string $fieldName, array $fieldDefinition, $context = null)
    {
        return StringUtil::specialchars($value);
    }
}
