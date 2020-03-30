<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\Bundle\DependencyInjection;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Config;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\Dbafs;
use Contao\Encryption;
use Contao\Environment;
use Contao\Frontend;
use Contao\FrontendUser;
use Contao\Input;
use Contao\Message;
use Contao\System;
use Contao\Controller;
use Netzmacht\Contao\Toolkit\Bundle\DependencyInjection\ContaoServicesFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContaoServicesFactorySpec extends ObjectBehavior
{
    function let(ContaoFrameworkInterface $framework)
    {
        $this->beConstructedWith($framework);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContaoServicesFactory::class);
    }

    function it_creates_backend_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Backend::class, $adapter);
        $this->createBackendAdapter()->shouldReturn($adapter);
    }

    function it_creates_frontend_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Frontend::class, $adapter);
        $this->createFrontendAdapter()->shouldReturn($adapter);
    }

    function it_creates_config_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Config::class, $adapter);
        $this->createConfigAdapter()->shouldReturn($adapter);
    }

    function it_creates_controller_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Controller::class, $adapter);
        $this->createControllerAdapter()->shouldReturn($adapter);
    }

    function it_creates_encryption_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Encryption::class, $adapter);
        $this->createEncryptionAdapter()->shouldReturn($adapter);
    }

    function it_creates_environment_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Environment::class, $adapter);
        $this->createEnvironmentAdapter()->shouldReturn($adapter);
    }

    function it_creates_input_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Input::class, $adapter);
        $this->createInputAdapter()->shouldReturn($adapter);
    }

    function it_creates_system_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, System::class, $adapter);
        $this->createSystemAdapter()->shouldReturn($adapter);
    }

    function it_creates_message_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Message::class, $adapter);
        $this->createMessageAdapter()->shouldReturn($adapter);
    }

    function it_creates_dbafs_adapter(ContaoFrameworkInterface $framework, Adapter $adapter)
    {
        $this->expectAdapterWillBeReturned($framework, Dbafs::class, $adapter);
        $this->createDbafsAdapter()->shouldReturn($adapter);
    }

    function it_creates_backend_user_instance(ContaoFrameworkInterface $framework, BackendUser $backendUser)
    {
        $this->expectInstanceWillBeCreated($framework, BackendUser::class, $backendUser);
        $this->createBackendUserInstance()->shouldReturn($backendUser);
    }

    function it_creates_frontend_user_instance(ContaoFrameworkInterface $framework, FrontendUser $frontendUser)
    {
        $this->expectInstanceWillBeCreated($framework, FrontendUser::class, $frontendUser);
        $this->createFrontendUserInstance()->shouldReturn($frontendUser);
    }

    function expectAdapterWillBeReturned(ContaoFrameworkInterface $framework, $class, Adapter $adapter)
    {
        $framework->initialize()->shouldBeCalled();
        $framework->getAdapter($class)->willReturn($adapter)->shouldBeCalled();
    }

    function expectInstanceWillBeCreated(ContaoFrameworkInterface $framework, $class, $instance)
    {
        $framework->initialize()->shouldBeCalled();
        $framework->createInstance($class)->willReturn($instance)->shouldBeCalled();
    }
}
