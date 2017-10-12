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

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;

/**
 * Class CreateFormatterSubscriber handles the create formatter event.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber
 */
final class CreateFormatterSubscriber
{
    /**
     * List of supported value formatter.
     *
     * @var array|ValueFormatter[]
     */
    private $formatter;

    /**
     * Value formatter pre filters.
     *
     * @var array|ValueFormatter[]
     */
    private $preFilters;

    /**
     * Value formatter post filters.
     *
     * @var array|ValueFormatter[]
     */
    private $postFilters;

    /**
     * Value formatter.
     *
     * @var ValueFormatter
     */
    private $optionsFormatter;

    /**
     * CreateFormatterSubscriber constructor.
     *
     * @param array|ValueFormatter[] $formatter        Value formatter.
     * @param array|ValueFormatter[] $preFilters       Pre filters.
     * @param array|ValueFormatter[] $postFilters      Post filters.
     * @param ValueFormatter         $optionsFormatter Options formatter.
     */
    public function __construct(
        array $formatter,
        array $preFilters,
        array $postFilters,
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
     *
     * @return void
     */
    public function handle(CreateFormatterEvent $event)
    {
        $event->addFormatter($this->formatter);
        $event->addPreFilters($this->preFilters);
        $event->addPostFilters($this->postFilters);

        if (!$event->getOptionsFormatter()) {
            $event->setOptionsFormatter($this->optionsFormatter);
        }
    }
}
