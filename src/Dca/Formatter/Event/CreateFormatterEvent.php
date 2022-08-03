<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Event;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Symfony\Contracts\EventDispatcher\Event;

final class CreateFormatterEvent extends Event
{
    public const NAME = 'netzmacht.contao_toolkit.dca.create_formatter';

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
    private $formatter = [];

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
     * @var ValueFormatter|null
     */
    private $optionsFormatter;

    /**
     * @param Definition $definition Data container definition.
     */
    public function __construct(Definition $definition)
    {
        $this->definition       = $definition;
        $this->optionsFormatter = null;
    }

    /**
     * Get definition.
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
        if ($formatter instanceof ValueFormatter) {
            $this->formatter[] = $formatter;

            return $this;
        }

        foreach ($formatter as $item) {
            $this->addFormatter($item);
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
     */
    public function getOptionsFormatter(): ?ValueFormatter
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
