<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Contracts\Translation\TranslatorInterface;

use function next;

class YesNoFormatterSpec extends ObjectBehavior
{
    public function let(TranslatorInterface $translator): void
    {
        $translator->trans('MSC.yes', [], 'contao_default', Argument::any())->willReturn('ja');
        $translator->trans('MSC.no', [], 'contao_default', Argument::any())->willReturn('nein');

        $this->beConstructedWith($translator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\YesNoFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_non_multiple_checkboxes(): void
    {
        $definition = ['inputType' => 'checkbox'];

        $this->accepts('test', $definition)->shouldReturn(true);

        $definition['eval']['multiple'] = false;
        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_multiple_checkboxes(): void
    {
        $definition = [
            'inputType' => 'checkbox',
            'eval'      => ['multiple' => true],
        ];

        $this->accepts('test', $definition)->shouldReturn(false);
    }

    public function it_does_not_accept_other_input_types(): void
    {
        $definition = [];
        $inputTypes = ['text', 'select', 'radio', 'password', 'textarea'];

        do {
            $this->accepts('test', $definition)->shouldReturn(false);

            $definition['inputType'] = next($inputTypes);
        } while ($definition['inputType']);
    }

    public function it_translate_checkbox_state(TranslatorInterface $translator): void
    {
        $translator->trans('MSC.yes', [], 'contao_default')->shouldBeCalled();
        $this->format('1', 'test', [])->shouldReturn('ja');

        $translator->trans('MSC.no', [], 'contao_default')->shouldBeCalled();
        $this->format('', 'test', [])->shouldReturn('nein');
    }
}
