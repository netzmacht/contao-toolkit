<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Interop\Container\ContainerInterface as Container;

/**
 * Class CreateFormatterSubscriber handles the create formatter event.
 *
 * @package Netzmacht\Contao\Toolkit\Dca\Formatter\Subscriber
 */
class CreateFormatterSubscriber
{
    /**
     * Service Container.
     *
     * @var Container
     */
    private $container;

    /**
     * List of service names which should be used as formatter and filters.
     *
     * @var array
     */
    private $serviceNames = [
        'formatter'    => [],
        'pre-filters'  => [],
        'post-filters' => []
    ];

    /**
     * CreateFormatterSubscriber constructor.
     *
     * @param Container $container    Service container.
     * @param array     $serviceNames Service names.
     */
    public function __construct(Container $container, array $serviceNames)
    {
        $this->container    = $container;
        $this->serviceNames = array_merge($this->serviceNames, $serviceNames);
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
        return $this->createFromServiceNames('formatter');
    }

    /**
     * Create all default pre filters.
     *
     * @return array
     */
    private function createPreFilters()
    {
        return $this->createFromServiceNames('pre-filters');
    }

    /**
     * Create all default post filters.
     *
     * @return array
     */
    private function createPostFilters()
    {
        return $this->createFromServiceNames('post-filters');
    }

    /**
     * Create the list of formatter by fetching the formatter from the service container.
     *
     * @param string $category Name of the category.
     *
     * @return array
     */
    private function createFromServiceNames($category)
    {
        $formatter = [];

        foreach ($this->serviceNames[$category] as $serviceName) {
            $formatter[] = $this->container->get($serviceName);
        }

        return $formatter;
    }
}
