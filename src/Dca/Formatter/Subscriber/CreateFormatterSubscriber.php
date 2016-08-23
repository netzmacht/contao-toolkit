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
    }

    /**
     * Create all default formatter.
     *
     * @return array
     */
    private function createFormatter()
    {
        return [
            new ForeignKeyFormatter(
                $this->container->get(Services::DATABASE_CONNECTION)
            ),
            new FileUuidFormatter(),
            new DateFormatter(
                $this->container->get(Services::CONFIG)
            ),
            new YesNoFormatter(
                $this->container->get(Services::TRANSLATOR)
            ),
            new HtmlFormatter(),
            new ReferenceFormatter(),
            new OptionsFormatter(
                $this->container->get(Services::CALLBACK_INVOKER)
            )
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
            new HiddenValueFormatter(),
            new DeserializeFormatter(),
            new EncryptedFormatter(
                $this->container->get(Services::ENCRYPTION)
            )
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
            new FlattenFormatter()
        ];
    }
}
