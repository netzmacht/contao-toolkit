<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias;

use Contao\CoreBundle\Slug\Slug;
use Netzmacht\Contao\Toolkit\Data\Alias\Exception\InvalidAliasException;
use Netzmacht\Contao\Toolkit\Data\Alias\SlugAliasGenerator;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use stdClass;

class SlugAliasGeneratorSpec extends ObjectBehavior
{
    public function let(Slug $slug, Validator $validator): void
    {
        $this->beConstructedWith($slug, $validator, 'tl_example', ['title']);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SlugAliasGenerator::class);
    }

    public function it_generates_a_slug_from_the_configured_fields_when_value_is_empty(
        Slug $slug,
        Validator $validator,
    ): void {
        $result        = new stdClass();
        $result->id    = 1;
        $result->title = 'Hello World';

        $slug
            ->generate('Hello World', ['delimiter' => '-'], Argument::type('callable'))
            ->willReturn('hello-world');

        $validator->validate($result, 'hello-world', [1])->willReturn(true);

        $this->generate($result, null)->shouldReturn('hello-world');
    }

    public function it_keeps_a_manually_set_unique_value(Slug $slug, Validator $validator): void
    {
        $result     = new stdClass();
        $result->id = 1;

        $validator->validate($result, 'custom-alias', [1])->willReturn(true);
        $slug->generate(Argument::cetera())->shouldNotBeCalled();

        $this->generate($result, 'custom-alias')->shouldReturn('custom-alias');
    }

    public function it_throws_when_a_manually_set_value_is_not_unique(Validator $validator): void
    {
        $result     = new stdClass();
        $result->id = 1;

        $validator->validate($result, 'duplicate', [1])->willReturn(false);

        $this->shouldThrow(InvalidAliasException::class)->during('generate', [$result, 'duplicate']);
    }

    public function it_throws_when_the_generated_value_is_still_not_unique(Slug $slug, Validator $validator): void
    {
        $result        = new stdClass();
        $result->id    = 1;
        $result->title = 'Hello World';

        $slug
            ->generate('Hello World', ['delimiter' => '-'], Argument::type('callable'))
            ->willReturn('hello-world');

        $validator->validate($result, 'hello-world', [1])->willReturn(false);

        $this->shouldThrow(InvalidAliasException::class)->during('generate', [$result, null]);
    }
}
