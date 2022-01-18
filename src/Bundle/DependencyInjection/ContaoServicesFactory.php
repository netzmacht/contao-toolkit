<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Bundle\DependencyInjection;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Dbafs;
use Contao\Encryption;
use Contao\Environment;
use Contao\Frontend;
use Contao\FrontendUser;
use Contao\Image;
use Contao\Input;
use Contao\Message;
use Contao\Model;
use Contao\System;

use function assert;

/**
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
     * @param ContaoFramework $framework Contao framework.
     */
    public function __construct(ContaoFramework $framework)
    {
        $this->framework = $framework;
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
     * Create an encryption adapter.
     *
     * @return Adapter<Encryption>
     */
    public function createEncryptionAdapter(): Adapter
    {
        return $this->createAdapter(Encryption::class);
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
     */
    public function createBackendUserInstance(): BackendUser
    {
        return $this->createInstance(BackendUser::class);
    }

    /**
     * Frontend user.
     */
    public function createFrontendUserInstance(): FrontendUser
    {
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

    // phpcs:disable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectOrderOfAnnotationsGroup

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

    // phpcs:enable SlevomatCodingStandard.Commenting.DocCommentSpacing.IncorrectOrderOfAnnotationsGroup
}
