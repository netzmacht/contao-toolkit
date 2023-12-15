<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\InsertTag;

use PhpSpec\ObjectBehavior;
use RuntimeException;

final class ArgumentParserSpec extends ObjectBehavior
{
    public function it_parses_arguments_default_splitted_by_double_colons(): void
    {
        $this->splitBy();
        $this->parse('foo::bar')->shouldReturn(['foo', 'bar']);
    }

    public function it_parses_arguments_splitted_by_custom_separator(): void
    {
        $this->splitBy('#');
        $this->parse('foo#bar')->shouldReturn(['foo', 'bar']);
    }

    public function it_parses_named_arguments(): void
    {
        $this->splitBy('#', ['firstname', 'lastname']);
        $this->parse('foo#bar')->shouldReturn(['firstname' => 'foo', 'lastname' => 'bar']);
    }

    public function it_limits_supported_arguments(): void
    {
        $this->splitBy('#', null, 2);
        $this->parse('foo#bar#baz')->shouldReturn(['foo', 'bar#baz']);
    }

    public function it_only_on_splitter_is_allowed(): void
    {
        $this->splitBy();

        $this->shouldThrow(RuntimeException::class)->during('splitBy');
    }

    public function it_parses_argument_query(): void
    {
        $this->parseQuery();
        $this->parse('?foo=bar+baz=2')->shouldReturn([['value' => '', 'options' => ['foo' => 'bar', 'baz' => '2']]]);
    }

    public function it_parses_argument_query_for_specific_indexed_argument(): void
    {
        $this->splitBy('::');
        $this->parseQuery([2]);
        $this
            ->parse('John::Doe::contacts?email=mail@example.org+website=https://example.org::?foo=bar')
            ->shouldReturn(
                [
                    'John',
                    'Doe',
                    [
                        'value'   => 'contacts',
                        'options' => [
                            'email'   => 'mail@example.org',
                            'website' => 'https://example.org',
                        ],
                    ],
                    '?foo=bar',
                ],
            );
    }

    public function it_parses_argument_query_for_specific_named_argument(): void
    {
        $this->splitBy('::', ['firstname', 'lastname', 'details', 'test']);
        $this->parseQuery(['details']);
        $this
            ->parse('John::Doe::contacts?email=mail@example.org+website=https://example.org::?foo=bar')
            ->shouldReturn(
                [
                    'firstname' => 'John',
                    'lastname'  => 'Doe',
                    'details'   => [
                        'value'   => 'contacts',
                        'options' => [
                            'email'   => 'mail@example.org',
                            'website' => 'https://example.org',
                        ],
                    ],
                    'test'      => '?foo=bar',
                ],
            );
    }
}
