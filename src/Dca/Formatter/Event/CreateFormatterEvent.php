<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Event;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class CreateFormatterEvent.
 *
 * @package Netzmacht\Contao\Toolkit\Event
 */
class CreateFormatterEvent extends Event
{
    const NAME = 'toolkit.dca.create-formatter';

    /**
     * Data container definition.
     *
     * @var Definition
     */
    private $definition;

    /**
     * Created formatter.
     *
     * @var ValueFormatter[]
     */
    private $formatter = array();

    /**
     * Pre filters.
     *
     * @var ValueFormatter[]
     */
    private $preFilters = [];

    /**
     * Post filters.
     *
     * @var ValueFormatter[]
     */
    private $postFilters = [];

    /**
     * CreateFormatterEvent constructor.
     *
     * @param Definition $definition Data container definition.
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
     * Add a formatter.
     *
     * @param ValueFormatter|ValueFormatter[] $formatter Formatter.
     *
     * @return $this
     */
    public function addFormatter(ValueFormatter $formatter)
    {
        if (is_array($formatter)) {
            foreach ($formatter as $item) {
                $this->addFormatter($item);
            }
        } else {
            $this->formatter[] = $formatter;
        }

        return $this;
    }

    /**
     * Get formatters.
     *
     * @return ValueFormatter[]
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Add a pre filter.
     *
     * @param ValueFormatter|ValueFormatter[] $formatter Formatter.
     *
     * @return $this
     */
    public function addPreFilter(ValueFormatter $formatter)
    {
        $this->preFilters[] = $formatter;

        return $this;
    }

    /**
     * Add pre filters.
     *
     * @param ValueFormatter[] $preFilters Pre filters.
     *
     * @return $this
     */
    public function addPreFilters(array $preFilters)
    {
        foreach ($preFilters as $filter) {
            $this->addPreFilter($filter);
        }

        return $this;
    }

    /**
     * Add a post filter.
     *
     * @param ValueFormatter|ValueFormatter[] $formatter Formatter.
     *
     * @return $this
     */
    public function addPostFilter(ValueFormatter $formatter)
    {
        $this->preFilters[] = $formatter;

        return $this;
    }

    /**
     * Add post filters.
     *
     * @param ValueFormatter[] $postFilters Post filters.
     *
     * @return $this
     */
    public function addPostFilters(array $postFilters)
    {
        foreach ($postFilters as $filter) {
            $this->addPostFilter($filter);
        }

        return $this;
    }

    /**
     * Get pre filters.
     *
     * @return ValueFormatter[]
     */
    public function getPreFilters()
    {
        return $this->preFilters;
    }

    /**
     * Get post filters.
     *
     * @return ValueFormatter[]
     */
    public function getPostFilters()
    {
        return $this->postFilters;
    }
}
