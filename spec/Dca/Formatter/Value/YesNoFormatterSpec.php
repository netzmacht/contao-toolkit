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

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\YesNoFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class YesNoFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 */
class YesNoFormatterSpec extends ObjectBehavior
{
    public function let(TranslatorInterface $translator)
    {
        $translator->trans('MSC.yes', [], 'contao_default', Argument::any())->willReturn('ja');
        $translator->trans('MSC.no', [], 'contao_default', Argument::any())->willReturn('nein');

        $this->beConstructedWith($translator);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\YesNoFormatter');
    }

    public function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_non_multiple_checkboxes()
    {
        $definition = [
            'inputType' => 'checkbox',
        ];

        $this->accepts('test', $definition)->shouldReturn(true);

        $definition['eval']['multiple'] = false;
        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_multiple_checkboxes()
    {
        $definition = [
            'inputType' => 'checkbox',
            'eval'      => ['multiple' => true]
        ];

        $this->accepts('test', $definition)->shouldReturn(false);
    }

    public function it_does_not_accept_other_input_types()
    {
        $definition = [];
        $inputTypes = ['text', 'select', 'radio', 'password', 'textarea'];

        do {
            $this->accepts('test', $definition)->shouldReturn(false);

            $definition['inputType'] = next($inputTypes);
        } while ($definition['inputType']);
    }

    public function it_translate_checkbox_state(TranslatorInterface $translator)
    {
        $translator->trans('MSC.yes', [], 'contao_default')->shouldBeCalled();
        $this->format('1', 'test', [])->shouldReturn('ja');

        $translator->trans('MSC.no', [], 'contao_default')->shouldBeCalled();
        $this->format('', 'test', [])->shouldReturn('nein');
    }
}
