<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FilterFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FormatterChain;
use Netzmacht\Contao\Toolkit\Exception\RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

use function sprintf;

/**
 * FormatterFactory creates the formatter for the data container definitions.
 */
final class FormatterFactory
{
    /**
     * Event dispatcher.
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher Event dispatcher.
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Create a formatter for a definition.
     *
     * @param Definition $definition Data container definition.
     */
    public function createFormatterFor(Definition $definition): Formatter
    {
        $event = new CreateFormatterEvent($definition);
        $this->eventDispatcher->dispatch($event, $event::NAME);

        $chainFilters     = [];
        $preFilters       = $event->getPreFilters();
        $formatter        = $event->getFormatter();
        $postFilters      = $event->getPostFilters();
        $optionsFormatter = $event->getOptionsFormatter();

        if ($preFilters) {
            $chainFilters[] = new FilterFormatter($preFilters);
        }

        if ($formatter) {
            $chainFilters[] = new FormatterChain($formatter);
        }

        if ($preFilters) {
            $chainFilters[] = new FilterFormatter($postFilters);
        }

        if ($optionsFormatter === null) {
            throw new RuntimeException(
                sprintf(
                    'Unable to create formatter for data container "%s". No options formatter exists.',
                    $definition->getName()
                )
            );
        }

        $chain = new FilterFormatter($chainFilters);

        return new ValueFormatterBasedFormatter($definition, $chain, $optionsFormatter);
    }
}
