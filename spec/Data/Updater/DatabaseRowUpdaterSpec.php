<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Updater;

use Contao\CoreBundle\Framework\Adapter;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Data\Updater\DatabaseRowUpdater;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use PhpSpec\ObjectBehavior;
use Symfony\Bundle\SecurityBundle\Security;

class DatabaseRowUpdaterSpec extends ObjectBehavior
{
    public function let(Security $security, Connection $connection, DcaManager $dcaManager, Adapter $systemAdapter): void
    {
        $this->beConstructedWith($security, $connection, $dcaManager, new Invoker($systemAdapter->getWrappedObject()));
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DatabaseRowUpdater::class);
    }

    public function it_grants_access_when_the_security_voter_grants_it(Security $security): void
    {
        $security->isGranted('contao_user.alexf', 'tl_example::title')->willReturn(true);

        $this->hasUserAccess('tl_example', 'title')->shouldReturn(true);
    }

    public function it_denies_access_when_the_security_voter_denies_it(Security $security): void
    {
        $security->isGranted('contao_user.alexf', 'tl_example::title')->willReturn(false);

        $this->hasUserAccess('tl_example', 'title')->shouldReturn(false);
    }
}
