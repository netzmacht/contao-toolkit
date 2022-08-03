<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;

/**
 * Class CreateFormatterSubscriber handles the create formatter event.
 */
final class CreateFormatterSubscriber
{
    /**
     * List of supported value formatter.
     *
     * @var ValueFormatter[]
     */
    private $formatter;

    /**
     * Value formatter pre filters.
     *
     * @var ValueFormatter[]
     */
    private $preFilters;

    /**
     * Value formatter post filters.
     *
     * @var ValueFormatter[]
     */
    private $postFilters;

    /**
     * Value formatter.
     *
     * @var ValueFormatter
     */
    private $optionsFormatter;

    /**
     * @param ValueFormatter[] $formatter        Value formatter.
     * @param ValueFormatter[] $preFilters       Pre filters.
     * @param ValueFormatter[] $postFilters      Post filters.
     * @param ValueFormatter   $optionsFormatter Options formatter.
     */
    public function __construct(
        iterable $formatter,
        iterable $preFilters,
        iterable $postFilters,
        ValueFormatter $optionsFormatter
    ) {
        $this->formatter        = $formatter;
        $this->preFilters       = $preFilters;
        $this->postFilters      = $postFilters;
        $this->optionsFormatter = $optionsFormatter;
    }

    /**
     * Handle the create formatter event.
     *
     * @param CreateFormatterEvent $event The handled event.
     */
    public function handle(CreateFormatterEvent $event): void
    {
        $event->addFormatter($this->formatter);
        $event->addPreFilters($this->preFilters);
        $event->addPostFilters($this->postFilters);

        if ($event->getOptionsFormatter()) {
            return;
        }

        $event->setOptionsFormatter($this->optionsFormatter);
    }
}
