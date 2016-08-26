<?php

/**
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2016 netzmacht David Molineus.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Interop\Container\ContainerInterface as Container;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DateFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DeserializeFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\EncryptedFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FileUuidFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FlattenFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ForeignKeyFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HiddenValueFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HtmlFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\OptionsFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ReferenceFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\YesNoFormatter;
use Netzmacht\Contao\Toolkit\DependencyInjection\Services;

/**
 * Class CreateFormatterSubscriber handles the create formatter event.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber
 */
final class CreateFormatterSubscriber
{
    /**
     * Service Container.
     *
     * @var Container
     */
    private $container;

    /**
     * CreateFormatterSubscriber constructor.
     *
     * @param Container $container Service container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
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
        $formatter   = $this->createFormatter();
        $preFilters  = $this->createPreFilters();
        $postFilters = $this->createPostFilters();

        $event->addFormatter($formatter);
        $event->addPreFilters($preFilters);
        $event->addPostFilters($postFilters);

        if (!$event->getOptionsFormatter()) {
            $event->setOptionsFormatter($this->container->get('toolkit.dca.formatter.options'));
        }
    }

    /**
     * Create all default formatter.
     *
     * @return array
     */
    private function createFormatter()
    {
        return [
            $this->container->get('toolkit.dca.formatter.foreign-key'),
            $this->container->get('toolkit.dca.formatter.file-uuid'),
            $this->container->get('toolkit.dca.formatter.date'),
            $this->container->get('toolkit.dca.formatter.yes-no'),
            $this->container->get('toolkit.dca.formatter.html'),
            $this->container->get('toolkit.dca.formatter.reference'),
            $this->container->get('toolkit.dca.formatter.options'),
        ];
    }

    /**
     * Create all default pre filters.
     *
     * @return array
     */
    private function createPreFilters()
    {
        return [
            $this->container->get('toolkit.dca.formatter.hidden'),
            $this->container->get('toolkit.dca.formatter.deserialize'),
            $this->container->get('toolkit.dca.formatter.encrypted'),
        ];
    }

    /**
     * Create all default post filters.
     *
     * @return array
     */
    private function createPostFilters()
    {
        return [
            $this->container->get('toolkit.dca.formatter.flatten'),
        ];
    }
}
