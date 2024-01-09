<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\DependencyInjection;

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
use Contao\System;
use Netzmacht\Contao\Toolkit\DependencyInjection\ContaoServicesFactory;
use PhpSpec\ObjectBehavior;

class ContaoServicesFactorySpec extends ObjectBehavior
{
    public function let(ContaoFramework $framework): void
    {
        $this->beConstructedWith($framework);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ContaoServicesFactory::class);
    }

    public function it_creates_backend_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Backend::class, $adapter);
        $this->createBackendAdapter()->shouldReturn($adapter);
    }

    public function it_creates_frontend_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Frontend::class, $adapter);
        $this->createFrontendAdapter()->shouldReturn($adapter);
    }

    public function it_creates_config_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Config::class, $adapter);
        $this->createConfigAdapter()->shouldReturn($adapter);
    }

    public function it_creates_controller_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Controller::class, $adapter);
        $this->createControllerAdapter()->shouldReturn($adapter);
    }

    public function it_creates_environment_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Environment::class, $adapter);
        $this->createEnvironmentAdapter()->shouldReturn($adapter);
    }

    public function it_creates_input_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Input::class, $adapter);
        $this->createInputAdapter()->shouldReturn($adapter);
    }

    public function it_creates_image_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Image::class, $adapter);
        $this->createImageAdapter()->shouldReturn($adapter);
    }

    public function it_creates_system_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, System::class, $adapter);
        $this->createSystemAdapter()->shouldReturn($adapter);
    }

    public function it_creates_message_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Message::class, $adapter);
        $this->createMessageAdapter()->shouldReturn($adapter);
    }

    public function it_creates_dbafs_adapter(ContaoFramework $framework, Adapter $adapter): void
    {
        $this->expectAdapterWillBeReturned($framework, Dbafs::class, $adapter);
        $this->createDbafsAdapter()->shouldReturn($adapter);
    }

    public function it_creates_backend_user_instance(
        ContaoFramework $framework,
        BackendUser $backendUser,
    ): void {
        $this->expectInstanceWillBeCreated($framework, BackendUser::class, $backendUser);
        $this->createBackendUserInstance()->shouldReturn($backendUser);
    }

    public function it_creates_frontend_user_instance(
        ContaoFramework $framework,
        FrontendUser $frontendUser,
    ): void {
        $this->expectInstanceWillBeCreated($framework, FrontendUser::class, $frontendUser);
        $this->createFrontendUserInstance()->shouldReturn($frontendUser);
    }

    public function expectAdapterWillBeReturned(
        ContaoFramework $framework,
        string $class,
        Adapter $adapter,
    ): void {
        $framework->initialize()->shouldBeCalled();
        $framework->getAdapter($class)->willReturn($adapter)->shouldBeCalled();
    }

    public function expectInstanceWillBeCreated(ContaoFramework $framework, string $class, object $instance): void
    {
        $framework->initialize()->shouldBeCalled();
        $framework->createInstance($class)->willReturn($instance)->shouldBeCalled();
    }
}
