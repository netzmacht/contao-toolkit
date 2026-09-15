<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Save;

use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\Save\GenerateAliasListener;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GenerateAliasListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    public function let(ContainerInterface $container, DcaManager $dcaManager): void
    {
        $this->beConstructedWith($container, $dcaManager, 'netzmacht.contao_toolkit.data.alias_generator.factory.default_factory');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(GenerateAliasListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'GenerateAliasListener is deprecated');
    }
}
