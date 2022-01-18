<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use PhpSpec\ObjectBehavior;

use function serialize;
use function uniqid;

class DeserializeFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\DeserializeFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_everything(): void
    {
        $this->accepts(uniqid(), [])->shouldReturn(true);
    }

    public function it_deserializes_arrays(): void
    {
        $value = ['test' => 'foo'];
        $raw   = serialize($value);

        $this->format($raw, 'test', [])->shouldReturn($value);
    }

    public function it_returns_scalar_values(): void
    {
        foreach ([true, false, null, 5, 5.6, 'foo'] as $value) {
            $this->format($value, 'test', [])->shouldReturn($value);
        }
    }
}
