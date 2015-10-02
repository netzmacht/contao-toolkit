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

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FormatterChain;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use Netzmacht\Contao\Toolkit\Event\CreateFormatterEvent;
use Netzmacht\Contao\Toolkit\ServiceContainer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * FormatterFactory creates the formatters for the data container definitions.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter
 */
class FormatterFactory
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ServiceContainer
     */
    private $serviceContainer;

    /**
     * Get the default formatter.
     *
     * @return ValueFormatter
     */
    public function getDefaultFormatter()
    {
        return $this->serviceContainer->getService('toolkit.dca-formatter.default');
    }

    /**
     * Create a formatter for a definition.
     *
     * @param Definition $definition Data container definition.
     *
     * @return ValueFormatter
     */
    public function createFormatterFor(Definition $definition)
    {
        if ($this->serviceContainer->hasService('toolkit.dca-formatter.' . $definition->getName())) {
            return $this->serviceContainer->getService('toolkit.dca-formatter.' . $definition->getName());
        }

        $event = new CreateFormatterEvent($definition);
        $this->eventDispatcher->dispatch($event::NAME, $event);

        if ($event->getFormatters()) {
            $local = new FormatterChain($event->getFormatters());

            return new FormatterChain([$local, $this->getDefaultFormatter()]);
        }

        return $this->getDefaultFormatter();
    }
}
