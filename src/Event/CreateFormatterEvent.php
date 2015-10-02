<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Event;


use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;

class CreateFormatterEvent
{
    const NAME = 'toolkit.dca.create-formatter';

    /**
     * Data container definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * Created formatters.
     *
     * @var ValueFormatter[]
     */
    private $formatters = array();

    /**
     * CreateFormatterEvent constructor.
     *
     * @param Definition $definition
     */
    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

    /**
     * Get definition.
     *
     * @return Definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @param ValueFormatter $formatter
     */
    public function addFormatter(ValueFormatter $formatter)
    {
        $this->formatters[] = $formatter;
    }

    /**
     * @param array $formatters
     */
    public function addFormatters(array $formatters)
    {
        foreach ($formatters as $formatter) {
            $this->addFormatter($formatter);
        }
    }

    /**
     * Get formatters.
     *
     * @return ValueFormatter[]
     */
    public function getFormatters()
    {
        return $this->formatters;
    }
}
