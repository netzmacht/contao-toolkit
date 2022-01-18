<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use PhpSpec\ObjectBehavior;

use function htmlentities;

class HtmlFormatterSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HtmlFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_fields_with_allow_html_option(): void
    {
        $definition['eval']['allowHtml'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_accepts_fields_preserve_tags_option(): void
    {
        $definition['eval']['preserveTags'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_a_field_by_default(): void
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    public function it_encodes_html_entities(): void
    {
        $test = '<b>Test</b> <a href="http://example.org">Example</a>';

        $this->format($test, 'test', [])->shouldReturn(htmlentities($test));
    }
}
