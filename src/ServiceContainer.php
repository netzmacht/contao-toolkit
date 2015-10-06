<?php

/**
 * @package    dev
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015 netzmacht creative David Molineus
 * @license    LGPL 3.0
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit;

use ContaoCommunityAlliance\Translator\TranslatorInterface;
use DependencyInjection\Container\PageProvider;
use Netzmacht\Contao\Toolkit\Dca\Manager;
use Netzmacht\Contao\Toolkit\InsertTag\Replacer;
use Netzmacht\Contao\Toolkit\View\AssetsManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The toolkit service container.
 *
 * @package Netzmacht\Contao\Toolkit
 */
class ServiceContainer
{
    /**
     * The service container.
     *
     * @var \Pimple
     */
    private $container;

    /**
     * ServiceContainer constructor.
     *
     * @param \Pimple $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Check if a service exists.
     *
     * @param string $service Service name.
     *
     * @return bool
     */
    public function hasService($service)
    {
        return isset($this->container[$service]);
    }

    /**
     * Get a service from the container.
     *
     * @param string $service The service name.
     *
     * @return mixed
     */
    public function getService($service)
    {
        return $this->container[$service];
    }

    /**
     * Get the dca manager.
     *
     * @return Manager
     */
    public function getDcaManager()
    {
        return $this->getService('toolkit.dca-manager');
    }

    /**
     * Get the translator.
     *
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->getService('translator');
    }

    /**
     * Get the assets manager.
     *
     * @return AssetsManager
     */
    public function getAssetsManager()
    {
        return $this->getService('toolkit.assets-manager');
    }

    /**
     * Get the database connection.
     *
     * @return \Database
     */
    public function getDatabaseConnection()
    {
        return $this->getService('database.connection');
    }

    /**
     * Get the session.
     *
     * @return \Session
     */
    public function getSession()
    {
        return $this->getService('session');
    }

    /**
     * Get the environment.
     *
     * @return \Environment
     */
    public function getEnvironment()
    {
        return $this->getService('environment');
    }

    /**
     * Get the input object.
     *
     * @return \Input
     */
    public function getInput()
    {
        return $this->getService('input');
    }

    /**
     * Get the user object for the current tl mode.
     *
     * @return \FrontendUser|\BackendUser
     */
    public function getUser()
    {
        return $this->getService('user');
    }

    /**
     * Get the config service.
     *
     * @return \Config
     */
    public function getConfig()
    {
        return $this->getService('config');
    }

    /**
     * Get the page provider.
     *
     * @return PageProvider
     */
    public function getPageProvider()
    {
        return $this->getService('page-provider');
    }

    /**
     * Get the event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->getService('event-dispatcher');
    }

    /**
     * Get the contao file system.
     *
     * @return \Files
     */
    public function getFileSystem()
    {
        return $this->getService('toolkit.filesystem');
    }

    /**
     * Get the insert tag replacer.
     *
     * @return Replacer
     */
    public function getInsertTagReplacer()
    {
        return $this->getService('toolkit.insert-tag-replacer');
    }
}
