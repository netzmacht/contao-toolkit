<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HtmlFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class HtmlFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin HtmlFormatter
 */
class HtmlFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\HtmlFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_fields_with_allow_html_option()
    {
        $definition['eval']['allowHtml'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_accepts_fields_preserve_tags_option()
    {
        $definition['eval']['preserveTags'] = true;

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_does_not_accept_a_field_by_default()
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    function it_encodes_html_entities()
    {
        $test = '<b>Test</b> <a href="http://example.org">Example</a>';

        $this->format($test, 'test', [])->shouldReturn(htmlentities($test));
    }
}
