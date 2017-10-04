<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\Config;

/**
 * DateFormatter format date values.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
final class DateFormatter implements ValueFormatter
{
    /**
     * Contao config.
     *
     * @var Config
     */
    private $config;

    /**
     * DateFormatter constructor.
     *
     * @param Config $config Contao config.
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function accepts($fieldName, array $fieldDefinition)
    {
        if ($fieldName === 'tstamp') {
            return true;
        }

        if (empty($fieldDefinition['eval']['rgxp'])) {
            return false;
        }

        return in_array($fieldDefinition['eval']['rgxp'], ['date', 'datim', 'time']);
    }

    /**
     * {@inheritDoc}
     */
    public function format($value, $fieldName, array $fieldDefinition, $context = null)
    {
        if (empty($fieldDefinition['eval']['rgxp'])) {
            $format = 'datim';
        } else {
            $format = $fieldDefinition['eval']['rgxp'];
        }

        $dateFormat = $this->config->get($format . 'Format');

        return \Date::parse($dateFormat, $value);
    }
}
