<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Contao\CoreBundle\Framework\Adapter;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use PhpSpec\ObjectBehavior;

//phpcs:disable Squiz.Classes.ClassFileName.NoMatch
//phpcs:disable Generic.Files.OneClassPerFile.MultipleFound
//phpcs:disable Squiz.Classes.ClassFileName.NoMatch
class OptionsFormatterSpec extends ObjectBehavior
{
    public function let(Adapter $adapter): void
    {
        $this->beConstructedWith(new Invoker($adapter->getWrappedObject()));
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\OptionsFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_fields_with_options(): void
    {
        $definition['options'] = ['foo' => 'bar'];

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_accepts_fields_with_is_associative_flag(): void
    {
        $definition['eval']['isAssociative'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_accepts_fields_with_options_callback(): void
    {
        $definition['options_callback'] = static function () {
            return [];
        };
        $this->accepts('test', $definition)->shouldReturn(true);

        $definition['options_callback'] = ['Foo', 'bar'];
        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_a_field_by_default(): void
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    public function it_formats_option_from_associative_array(): void
    {
        $definition = [
            'options' => ['test' => 'foo'],
        ];

        $this->format('test', 'bar', $definition)->shouldReturn('foo');
    }

    public function it_formats_option_of_associative_flagged_field(): void
    {
        $definition = [
            'options' => [1 => 'foo'],
            'eval'    => ['isAssociative' => true],
        ];

        $this->format(1, 'test', $definition)->shouldReturn('foo');
    }

    public function it_formats_option_calling_options_callback(): void
    {
        $definition = [
            'options_callback' => static function () {
                return ['foo' => 'Bar'];
            },
        ];

        $this->format('foo', 'test', $definition)->shouldReturn('Bar');
    }
}
