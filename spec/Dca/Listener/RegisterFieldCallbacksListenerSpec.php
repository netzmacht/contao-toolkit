<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener;

use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\RegisterFieldCallbacksListener;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use RuntimeException;

class RegisterFieldCallbacksListenerSpec extends ObjectBehavior
{
    public function let(DcaManager $dcaManager, RequestScopeMatcher $scopeMatcher): void
    {
        $callbacks = [
            'template_options' => [
                'slot' => 'options_callback',
                'service' => 'App\TemplateOptionsListener',
                'method' => 'onOptionsCallback',
            ],
            'alias_generator' => [
                'slot' => 'save_callback',
                'service' => 'App\SlugAliasListener',
                'method' => 'onSaveCallback',
            ],
        ];

        $this->beConstructedWith($dcaManager, $scopeMatcher, $callbacks);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RegisterFieldCallbacksListener::class);
    }

    public function it_does_nothing_outside_a_contao_request(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $scopeMatcher->isContaoRequest()->willReturn(false);
        $dcaManager->getDefinition(Argument::any())->shouldNotBeCalled();

        $this->onLoadDataContainer('tl_example');
    }

    public function it_registers_a_single_slot_callback_when_none_is_set(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $dca        = ['fields' => ['template' => ['toolkit' => ['template_options' => ['prefix' => 'ce_']]]]];
        $definition = new Definition('tl_content', $dca);

        $scopeMatcher->isContaoRequest()->willReturn(true);
        $dcaManager->getDefinition('tl_content')->willReturn($definition);

        $this->onLoadDataContainer('tl_content');

        if ($definition->get(['fields', 'template', 'options_callback']) !== ['App\TemplateOptionsListener', 'onOptionsCallback']) {
            throw new RuntimeException('Expected options_callback to be auto-registered.');
        }
    }

    public function it_does_not_overwrite_an_already_set_single_slot_callback(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $dca = [
            'fields' => [
                'template' => [
                    'toolkit' => ['template_options' => ['prefix' => 'ce_']],
                    'options_callback' => ['App\CustomListener', 'onCustom'],
                ],
            ],
        ];
        $definition = new Definition('tl_content', $dca);

        $scopeMatcher->isContaoRequest()->willReturn(true);
        $dcaManager->getDefinition('tl_content')->willReturn($definition);

        $this->onLoadDataContainer('tl_content');

        if ($definition->get(['fields', 'template', 'options_callback']) !== ['App\CustomListener', 'onCustom']) {
            throw new RuntimeException('Did not expect the manual options_callback to be overwritten.');
        }
    }

    public function it_appends_a_list_slot_callback_without_duplicating_it(
        RequestScopeMatcher $scopeMatcher,
        DcaManager $dcaManager,
    ): void {
        $dca = [
            'fields' => [
                'alias' => [
                    'toolkit' => ['alias_generator' => ['fields' => ['title']]],
                    'save_callback' => [['App\ExistingListener', 'onSave']],
                ],
            ],
        ];
        $definition = new Definition('tl_content', $dca);

        $scopeMatcher->isContaoRequest()->willReturn(true);
        $dcaManager->getDefinition('tl_content')->willReturn($definition);

        $this->onLoadDataContainer('tl_content');
        $this->onLoadDataContainer('tl_content');

        $expected = [
            ['App\ExistingListener', 'onSave'],
            ['App\SlugAliasListener', 'onSaveCallback'],
        ];

        if ($definition->get(['fields', 'alias', 'save_callback']) !== $expected) {
            throw new RuntimeException('Expected the save_callback to be appended exactly once, even across two loadDataContainer calls.');
        }
    }
}
