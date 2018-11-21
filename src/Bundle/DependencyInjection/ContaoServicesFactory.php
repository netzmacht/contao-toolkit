<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface as ContaoFramework;
use Contao\Encryption;
use Contao\Environment;
use Contao\Frontend;
use Contao\FrontendUser;
use Contao\Input;
use Contao\Model;
use Contao\System;

/**
 * Class ContaoServicesFactory.
 *
 * @package Netzmacht\Contao\Toolkit\DependencyInjection
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class ContaoServicesFactory
{
    /**
     * Contao framework.
     *
     * @var ContaoFramework
     */
    private $framework;

    /**
     * ContaoServicesFactory constructor.
     *
     * @param ContaoFramework $framework Contao framework.
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
    }

    /**
     * Create the backend adapter.
     *
     * @return Adapter|Backend
     */
    public function createBackendAdapter(): Adapter
    {
        return $this->createAdapter(Backend::class);
    }

    /**
     * Create input adapter.
     *
     * @return Adapter|Input
     */
    public function createInputAdapter(): Adapter
    {
        return $this->createAdapter(Input::class);
    }

    /**
     * Create the config adapter.
     *
     * @return Adapter|Config
     */
    public function createConfigAdapter(): Adapter
    {
        return $this->createAdapter(Config::class);
    }

    /**
     * Create the controller adapter.
     *
     * @return Adapter|Controller
     */
    public function createControllerAdapter(): Adapter
    {
        return $this->createAdapter(Controller::class);
    }

    /**
     * Create the system adapter.
     *
     * @return Adapter
     */
    public function createSystemAdapter(): Adapter
    {
        return $this->createAdapter(System::class);
    }

    /**
     * Create an environment adapter.
     *
     * @return Adapter|Environment
     */
    public function createEnvironmentAdapter(): Adapter
    {
        return $this->createAdapter(Environment::class);
    }

    /**
     * Create an environment adapter.
     *
     * @return Adapter|Encryption
     */
    public function createEncryptionAdapter(): Adapter
    {
        return $this->createAdapter(Encryption::class);
    }

    /**
     * Create a frontend adapter.
     *
     * @return Adapter|Frontend
     */
    public function createFrontendAdapter(): Adapter
    {
        return $this->createAdapter(Frontend::class);
    }

    /**
     * Create backend user instance.
     *
     * @return BackendUser
     */
    public function createBackendUserInstance(): BackendUser
    {
        return $this->createInstance(BackendUser::class);
    }

    /**
     * Frontend user.
     *
     * @return FrontendUser
     */
    public function createFrontendUserInstance(): FrontendUser
    {
        return $this->createInstance(FrontendUser::class);
    }

    /**
     * Create a model adapter.
     *
     * @return Adapter|Model\
     */
    public function createModelAdapter(): Adapter
    {
        return $this->createAdapter(Model::class);
    }

    /**
     * Create an adapter for a specific class.
     *
     * @param string $class Class name.
     *
     * @return Adapter
     */
    private function createAdapter(string $class): Adapter
    {
        $this->framework->initialize();

        return $this->framework->getAdapter($class);
    }

    /**
     * Create an adapter for a specific class.
     *
     * @param string $class Class name.
     *
     * @return object
     */
    private function createInstance(string $class)
    {
        $this->framework->initialize();

        return $this->framework->createInstance($class);
    }
}
