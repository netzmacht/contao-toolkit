<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\DependencyInjection;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Dbafs;
use Contao\Environment;
use Contao\Frontend;
use Contao\FrontendUser;
use Contao\Image;
use Contao\Input;
use Contao\Message;
use Contao\Model;
use Contao\System;

use function assert;
use function trigger_deprecation;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
final class ContaoServicesFactory
{
    /** @param ContaoFramework $framework Contao framework. */
    public function __construct(private readonly ContaoFramework $framework)
    {
    }

    /**
     * Create the backend adapter.
     *
     * @return Adapter<Backend>
     */
    public function createBackendAdapter(): Adapter
    {
        return $this->createAdapter(Backend::class);
    }

    /**
     * Create input adapter.
     *
     * @return Adapter<Input>
     */
    public function createInputAdapter(): Adapter
    {
        return $this->createAdapter(Input::class);
    }

    /**
     * Create the config adapter.
     *
     * @return Adapter<Config>
     */
    public function createConfigAdapter(): Adapter
    {
        return $this->createAdapter(Config::class);
    }

    /**
     * Create the controller adapter.
     *
     * @return Adapter<Controller>
     */
    public function createControllerAdapter(): Adapter
    {
        return $this->createAdapter(Controller::class);
    }

    /**
     * Create the system adapter.
     *
     * @return Adapter<System>
     */
    public function createSystemAdapter(): Adapter
    {
        return $this->createAdapter(System::class);
    }

    /**
     * Create an environment adapter.
     *
     * @return Adapter<Environment>
     */
    public function createEnvironmentAdapter(): Adapter
    {
        return $this->createAdapter(Environment::class);
    }

    /**
     * Create a frontend adapter.
     *
     * @return Adapter<Frontend>
     */
    public function createFrontendAdapter(): Adapter
    {
        return $this->createAdapter(Frontend::class);
    }

    /**
     * Create an image adapter.
     *
     * @return Adapter<Image>
     */
    public function createImageAdapter(): Adapter
    {
        return $this->createAdapter(Image::class);
    }

    /**
     * Create backend user instance.
     *
     * @deprecated Use Symfony\Bundle\SecurityBundle\Security::isGranted() for permission checks,
     *             or ::getUser() for the concrete user object, instead. Will be removed in 5.0.
     */
    public function createBackendUserInstance(): BackendUser
    {
        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'ContaoServicesFactory::createBackendUserInstance() is deprecated. Use Symfony\Bundle\SecurityBundle\Security::isGranted() or ::getUser() instead.',
        );

        return $this->createInstance(BackendUser::class);
    }

    /**
     * Frontend user.
     *
     * @deprecated Use Symfony\Bundle\SecurityBundle\Security::isGranted() for permission checks,
     *             or ::getUser() for the concrete user object, instead. Will be removed in 5.0.
     */
    public function createFrontendUserInstance(): FrontendUser
    {
        trigger_deprecation(
            'netzmacht/contao-toolkit',
            '4.1',
            'ContaoServicesFactory::createFrontendUserInstance() is deprecated. Use Symfony\Bundle\SecurityBundle\Security::isGranted() or ::getUser() instead.',
        );

        return $this->createInstance(FrontendUser::class);
    }

    /**
     * Create a model adapter.
     *
     * @return Adapter<Model>
     */
    public function createModelAdapter(): Adapter
    {
        return $this->createAdapter(Model::class);
    }

    /**
     * Create a message adapter.
     *
     * @return Adapter<Message>
     */
    public function createMessageAdapter(): Adapter
    {
        return $this->createAdapter(Message::class);
    }

    /**
     * Create a message adapter.
     *
     * @return Adapter<Dbafs>
     */
    public function createDbafsAdapter(): Adapter
    {
        return $this->createAdapter(Dbafs::class);
    }

    /**
     * Create an adapter for a specific class.
     *
     * @template T
     *
     * @param class-string<T> $class Class name.
     *
     * @return Adapter<T>
     */
    private function createAdapter(string $class): Adapter
    {
        $this->framework->initialize();

        return $this->framework->getAdapter($class);
    }

    /**
     * Create an adapter for a specific class.
     *
     * @template T
     *
     * @param class-string<T> $class Class name.
     *
     * @return T
     */
    private function createInstance(string $class): object
    {
        $this->framework->initialize();

        $instance = $this->framework->createInstance($class);
        assert($instance instanceof $class);

        return $instance;
    }
}
