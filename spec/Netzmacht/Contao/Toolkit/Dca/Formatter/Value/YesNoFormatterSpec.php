<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\YesNoFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class YesNoFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin YesNoFormatter
 */
class YesNoFormatterSpec extends ObjectBehavior
{
    function let(TranslatorInterface $translator)
    {
        $translator->translate('yes', 'MSC', Argument::any())->willReturn('ja');
        $translator->translate('no', 'MSC', Argument::any())->willReturn('nein');

        $this->beConstructedWith($translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\YesNoFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_non_multiple_checkboxes()
    {
        $definition = [
            'inputType' => 'checkbox',
        ];

        $this->accepts('test', $definition)->shouldReturn(true);

        $definition['eval']['multiple'] = false;
        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_does_not_accept_multiple_checkboxes()
    {
        $definition = [
            'inputType' => 'checkbox',
            'eval'      => ['multiple' => true]
        ];

        $this->accepts('test', $definition)->shouldReturn(false);
    }

    function it_does_not_accept_other_input_types()
    {
        $definition = [];
        $inputTypes = ['text', 'select', 'radio', 'password', 'textarea'];

        do {
            $this->accepts('test', $definition)->shouldReturn(false);

            $definition['inputType'] = next($inputTypes);
        } while ($definition['inputType']);
    }

    function it_translate_checkbox_state(TranslatorInterface $translator)
    {
        $translator->translate('yes', 'MSC')->shouldBeCalled();
        $this->format('1', 'test', [])->shouldReturn('ja');

        $translator->translate('no', 'MSC')->shouldBeCalled();
        $this->format('', 'test', [])->shouldReturn('nein');
    }
}
