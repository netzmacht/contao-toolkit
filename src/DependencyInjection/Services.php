<?php

/**
 * @package    netzmacht
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2016 netzmacht David Molineus. All rights reserved.
 * @filesource
 *
 */

namespace Netzmacht\Contao\Toolkit\DependencyInjection;

/**
 * Class ToolkitServices.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection
 */
final class Services
{
    /**
     * Assets manager service.
     *
     * Is an instance of Netzmacht\Contao\Toolkit\View\Assets\AssetsManager
     *
     * @var string
     */
    const ASSETS_MANAGER = 'toolkit.view.assets-manager';

    /**
     * Callback invoker service.
     *
     * Instance of Netzmacht\Contao\Toolkit\Dca\Callback\Invoker
     *
     * @var string
     */
    const CALLBACK_INVOKER = 'toolkit.dca.callback-invoker';

    /**
     * Config service.
     *
     * Instance of \Config.
     *
     * @var string
     */
    const CONFIG = 'config';

    /**
     * The container provides access to the toolkit container.
     *
     * Instance of Interop\Container\ContainerInterface.
     *
     * @var string
     */
    const CONTAINER = 'toolkit.container';

    /**
     * Content element factory.
     *
     * Instance of Netzmacht\Contao\Toolkit\Component\ComponentFactory
     *
     * @var string
     */
    const CONTENT_ELEMENT_FACTORY = 'toolkit.component.content-element-factory';

    /**
     * Content elements factories map.
     *
     * Instance of ArrayObject
     *
     * @var string
     */
    const CONTENT_ELEMENTS_MAP = 'toolkit.component.elements';

    /**
     * Database connection service.
     *
     * Instance of \Database
     *
     * @var string
     */
    const DATABASE_CONNECTION = 'database.connection';

    /**
     * Data container manager service.
     *
     * Instance of Netzmacht\Contao\Toolkit\Dca\Manager.
     *
     * @var string
     */
    const DCA_MANAGER = 'toolkit.dca.manager';

    /**
     * Default alias generator factory.
     *
     * @var string
     */
    const DEFAULT_ALIAS_GENERATOR_FACTORY = 'toolkit.data.alias-generator.factory.default';

    /**
     * Contao encryption as a service.
     *
     * Instance of Encryption.
     *
     * @var string
     */
    const ENCRYPTION = 'toolkit.contao.encryption';

    /**
     * Environment service.
     *
     * Instance of \Environment.
     *
     * @var string
     */
    const ENVIRONMENT = 'environment';

    /**
     * Event dispatcher service.
     *
     * Instance of Symfony\Component\EventDispatcher\EventDispatcherInterface.
     *
     * @var string
     */
    const EVENT_DISPATCHER = 'event-dispatcher';

    /**
     * Filesystem service.
     *
     * Instance of \Files.
     *
     * @var string
     */
    const FILE_SYSTEM = 'toolkit.filesystem';

    /**
     * Request Input service.
     *
     * Instance of \Input.
     *
     * @var string
     */
    const INPUT = 'input';

    /**
     * Insert tag replacer service.
     *
     * Instance of Netzmacht\Contao\Toolkit\InsertTag\Replacer.
     *
     * @var string
     */
    const INSERT_TAG_REPLACER = 'toolkit.insert-tag.replacer';

    /**
     * Module factory.
     *
     * Instance of Netzmacht\Contao\Toolkit\Component\ComponentFactory
     *
     * @var string
     */
    const MODULE_FACTORY = 'toolkit.component.module-factory';

    /**
     * Module factories map.
     *
     * Instance of ArrayObject
     *
     * @var string
     */
    const MODULES_MAP = 'toolkit.component.modules';

    /**
     * Page provider Service.
     *
     * Instance of DependencyInjection\Container\PageProvider.
     *
     * @var string
     */
    const PAGE_PROVIDER = 'page-provider';

    /**
     * Production mode setting.
     *
     * Boolean.
     *
     * @var string
     */
    const PRODUCTION_MODE = 'toolkit.production-mode';

    /**
     * Request token service.
     *
     * Instnace of \RequestToken.
     *
     * @var string
     */
    const REQUEST_TOKEN = 'request-token';

    /**
     * Session service.
     *
     * Instance of \Session.
     *
     * @var string
     */
    const SESSION = 'session';

    /**
     * State toggle service.
     *
     * Instance of Netzmacht\Contao\Toolkit\Data\State\StateToggle
     *
     * @var string
     */
    const STATE_TOGGLE = 'toolkit.data.state-toggle-factory';

    /**
     * Template factory service.
     *
     * Instance of Netzmacht\Contao\Toolkit\View\TemplateFactory.
     *
     * @var string
     */
    const TEMPLATE_FACTORY = 'toolkit.view.template-factory';

    /**
     * Translator service name.
     *
     * Instance of ContaoCommunityAlliance\Translator\TranslatorInterface,
     *
     * @var string
     */
    const TRANSLATOR = 'translator';

    /**
     * Map of all template helpers.
     *
     * Is an array of callable
     *
     * @var string
     */
    const VIEW_HELPERS = 'toolkit.view.template-helpers';

    /**
     * Frontend/backend user service.
     *
     * Instance of \User.
     *
     * @var string
     */
    const USER = 'user';
}
