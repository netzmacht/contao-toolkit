<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\SetOperationDataAttributeListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RuntimeException;

class SetOperationDataAttributeListenerSpec extends ObjectBehavior
{
    public function let(DcaManager $dcaManager, ScopeMatcher $scopeMatcher): void
    {
        $this->beConstructedWith($dcaManager, $scopeMatcher);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SetOperationDataAttributeListener::class);
    }

    public function it_does_nothing_outside_a_contao_request(
        ScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $scopeMatcher->isContaoRequest()->willReturn(false);
        $dcaManager->getDefinition(Argument::any())->shouldNotBeCalled();

        $this->onLoadDataContainer('tl_example');
    }

    public function it_sets_the_data_operation_attribute_for_toolkit_operations(
        ScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $dca = [
            'list' => [
                'operations' => [
                    'toggle' => ['toolkit' => ['state_button' => []]],
                    'edit' => [],
                ],
            ],
        ];

        $definition = new Definition('tl_example', $dca);

        $scopeMatcher->isContaoRequest()->willReturn(true);
        $dcaManager->getDefinition('tl_example')->willReturn($definition);

        $this->onLoadDataContainer('tl_example');

        if ($definition->get(['list', 'operations', 'toggle', 'attributes']) !== 'data-operation="toggle"') {
            throw new RuntimeException('Expected data-operation attribute to be set on the "toggle" operation.');
        }

        if ($definition->get(['list', 'operations', 'edit', 'attributes'], null) !== null) {
            throw new RuntimeException('Did not expect an attribute on the "edit" operation (no toolkit config).');
        }
    }
}
