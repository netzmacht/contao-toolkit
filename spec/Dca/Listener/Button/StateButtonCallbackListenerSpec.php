<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Button;

use Contao\Backend;
use Contao\CoreBundle\Framework\Adapter;
use Contao\Input;
use Netzmacht\Contao\Toolkit\Data\Updater\Updater;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Listener\Button\StateButtonCallbackListener;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

class StateButtonCallbackListenerSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    /**
     * @param Adapter<Backend> $backend
     * @param Adapter<Input>   $input
     */
    public function let(Adapter $backend, Adapter $input, Updater $updater, DcaManager $dcaManager): void
    {
        $this->beConstructedWith($backend, $input, $updater, $dcaManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(StateButtonCallbackListener::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'StateButtonCallbackListener is deprecated');
    }
}
