<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\FlattenFormatter;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use PhpSpec\ObjectBehavior;

class FlattenFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FlattenFormatter::class);
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement(ValueFormatter::class);
    }

    public function it_accepts_multiple_fields(): void
    {
        $definition['eval']['multiple'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_non_multiple_fields(): void
    {
        $definition['eval']['multiple'] = false;
        $this->accepts('test', $definition)->shouldReturn(false);
        $this->accepts('test', [])->shouldReturn(false);
    }

    public function it_does_not_accept_a_field_by_default(): void
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    public function it_flattens_arrays_to_csv(): void
    {
        $value = ['a', 'b'];

        $this->format($value, 'test', [])->shouldReturn('a, b');
    }

    public function it_flattens_nested_arrays(): void
    {
        $value = ['a', ['b', 'c']];

        $this->format($value, 'test', [])->shouldReturn('a, [b, c]');
    }

    public function it_flattens_objects(): void
    {
        $value = (object) ['a' => 'a', 'b' => ['b', 'c']];

        $this->format($value, 'test', [])->shouldReturn('a, [b, c]');
    }

    public function it_bypasses_scalar_values(): void
    {
        foreach (['a', 1.3, 5, true, false, null] as $value) {
            $this->format($value, 'test', [])->shouldReturn($value);
        }
    }
}
