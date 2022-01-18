<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use PhpSpec\ObjectBehavior;

class HiddenValueFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HiddenValueFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_password_field(): void
    {
        $definition['inputType'] = 'password';

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_accepts_do_not_show_option(): void
    {
        $definition['eval']['doNotShow'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_accepts_hide_input_option(): void
    {
        $definition['eval']['hideInput'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_a_field_by_default(): void
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    public function it_hides_value(): void
    {
        $this->format('test', 'test', [])->shouldReturn('');
    }

    public function it_has_a_password_mask_option_for_password_fields(): void
    {
        $this->beConstructedWith('*****');
        $definition['inputType'] = 'password';

        $this->format('test', 'test', $definition)->shouldReturn('*****');
    }

    public function it_ignores_the_password_mask_option_for_non_password_fields(): void
    {
        $this->beConstructedWith('*****');
        $definition['inputType'] = 'text';

        $this->format('test', 'test', $definition)->shouldReturn('');
    }
}
