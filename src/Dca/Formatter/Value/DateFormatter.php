<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\Config;

/**
 * DateFormatter format date values.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class DateFormatter implements ValueFormatter
{
    /**
     * @var Config
     */
    private $config;

    /**
     * DateFormatter constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritDoc}
     */
    public function accept($fieldName, array $fieldDefinition)
    {
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
        $dateFormat = $this->config->get($fieldDefinition['eval']['rgxp'] . 'Format');

        return \Date::parse($dateFormat, $value);
    }
}
