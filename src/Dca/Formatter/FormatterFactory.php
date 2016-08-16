<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter;

use Interop\Container\ContainerInterface;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FilterFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FormatterChain;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * FormatterFactory creates the formatters for the data container definitions.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter
 */
class FormatterFactory
{
    /**
     * Event dispatcher.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * Service container.
     *
     * @var ContainerInterface
     */
    private $serviceContainer;

    /**
     * FormatterFactory constructor.
     *
     * @param ContainerInterface       $serviceContainer Event dispatcher.
     * @param EventDispatcherInterface $eventDispatcher  Service container.
     */
    public function __construct(ContainerInterface $serviceContainer, EventDispatcherInterface $eventDispatcher)
    {
        $this->serviceContainer = $serviceContainer;
        $this->eventDispatcher  = $eventDispatcher;
    }

    /**
     * Get the default formatter.
     *
     * @return ValueFormatter
     */
    public function getDefaultValueFormatter()
    {
        return $this->serviceContainer->get('toolkit.dca-formatter.default');
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

        $chainFilters = [];
        $preFilters   = $event->getPreFilters();
        $formatter    = $event->getFormatter();
        $postFilters  = $event->getPostFilters();

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

        return new Formatter($definition, $chain);
    }
}
