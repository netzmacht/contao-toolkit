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

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Event;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Symfony\Component\EventDispatcher\Event;
use Webmozart\Assert\Assert;

/**
 * Class CreateFormatterEvent.
 *
 * @package Netzmacht\Contao\Toolkit\Event
 */
final class CreateFormatterEvent extends Event
{
    const NAME = 'netzmacht.contao_toolkit.dca.create_formatter';

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
     * Options formatter.
     *
     * @var ValueFormatter
     */
    private $optionsFormatter;

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
    public function getDefinition(): Definition
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
    public function addFormatter($formatter): self
    {
        if (is_array($formatter)) {
            foreach ($formatter as $item) {
                $this->addFormatter($item);
            }
        } else {
            Assert::isInstanceOf($formatter, 'Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');

            $this->formatter[] = $formatter;
        }

        return $this;
    }

    /**
     * Get formatter.
     *
     * @return array|ValueFormatter[]
     */
    public function getFormatter(): array
    {
        return $this->formatter;
    }

    /**
     * Add a pre filter.
     *
     * @param ValueFormatter $formatter Formatter.
     *
     * @return $this
     */
    public function addPreFilter(ValueFormatter $formatter): self
    {
        $this->preFilters[] = $formatter;

        return $this;
    }

    /**
     * Add pre filters.
     *
     * @param array|ValueFormatter[] $preFilters Pre filters.
     *
     * @return $this
     */
    public function addPreFilters(array $preFilters): self
    {
        foreach ($preFilters as $filter) {
            $this->addPreFilter($filter);
        }

        return $this;
    }

    /**
     * Add a post filter.
     *
     * @param ValueFormatter $formatter Formatter.
     *
     * @return $this
     */
    public function addPostFilter(ValueFormatter $formatter): self
    {
        $this->preFilters[] = $formatter;

        return $this;
    }

    /**
     * Add post filters.
     *
     * @param array|ValueFormatter[] $postFilters Post filters.
     *
     * @return $this
     */
    public function addPostFilters(array $postFilters): self
    {
        foreach ($postFilters as $filter) {
            $this->addPostFilter($filter);
        }

        return $this;
    }

    /**
     * Get pre filters.
     *
     * @return array|ValueFormatter[]
     */
    public function getPreFilters(): array
    {
        return $this->preFilters;
    }

    /**
     * Get post filters.
     *
     * @return array|ValueFormatter[]
     */
    public function getPostFilters(): array
    {
        return $this->postFilters;
    }

    /**
     * Get options formatter.
     *
     * @return ValueFormatter|null
     */
    public function getOptionsFormatter()
    {
        return $this->optionsFormatter;
    }

    /**
     * Set options formatter.
     *
     * @param ValueFormatter $optionsFormatter Options formatter.
     *
     * @return $this
     */
    public function setOptionsFormatter(ValueFormatter $optionsFormatter): self
    {
        $this->optionsFormatter = $optionsFormatter;

        return $this;
    }
}
