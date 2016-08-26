<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

use Interop\Container\ContainerInterface as Container;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FilterFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FormatterChain;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as EventDispatcher;

/**
 * FormatterFactory creates the formatter for the data container definitions.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter
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
     * Service container.
     *
     * @var Container
     */
    private $serviceContainer;

    /**
     * FormatterFactory constructor.
     *
     * @param Container       $serviceContainer Event dispatcher.
     * @param EventDispatcher $eventDispatcher  Service container.
     */
    public function __construct(Container $serviceContainer, EventDispatcher $eventDispatcher)
    {
        $this->serviceContainer = $serviceContainer;
        $this->eventDispatcher  = $eventDispatcher;
    }

    /**
     * Create a formatter for a definition.
     *
     * @param Definition $definition Data container definition.
     *
     * @return Formatter
     */
    public function createFormatterFor(Definition $definition)
    {
        $event = new CreateFormatterEvent($definition);
        $this->eventDispatcher->dispatch($event::NAME, $event);

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

        $chain = new FilterFormatter($chainFilters);

        return new Formatter($definition, $chain, $optionsFormatter);
    }
}
