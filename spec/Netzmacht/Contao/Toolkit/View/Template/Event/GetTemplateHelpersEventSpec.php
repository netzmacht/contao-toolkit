<?php

namespace spec\Netzmacht\Contao\Toolkit\View\Template\Event;

use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use PhpSpec\ObjectBehavior;

/**
 * Class GetTemplateHelpersEventSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\View\Template\Event
 * @mixin GetTemplateHelpersEvent
 */
class GetTemplateHelpersEventSpec extends ObjectBehavior
{
    const TEMPLATE_NAME = 'template_name';

    const CONTENT_TYPE = 'content/type';

    function let()
    {
        $this->beConstructedWith(static::TEMPLATE_NAME, static::CONTENT_TYPE);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent');
    }

    function it_knows_template_name()
    {
        $this->getTemplateName()->shouldReturn(static::TEMPLATE_NAME);

    }

    function it_knows_content_type()
    {
        $this->getContentType()->shouldReturn(static::CONTENT_TYPE);
    }

    function it_adds_helper()
    {
        $this->addHelper('foo', 'bar')->shouldReturn($this);
        $this->getHelpers()->shouldReturn(['foo' => 'bar']);
    }

    function it_adds_helpers()
    {
        $helpers = [
            'foo' => 'fooHelper',
            'bar' => 'barHelper'
        ];

        $this->addHelpers($helpers)->shouldReturn($this);
        $this->getHelpers()->shouldReturn($helpers);
    }

    function it_only_store_latest_named_helper()
    {
        $this->addHelper('foo', 'fooHelper');
        $this->getHelpers()->shouldReturn(['foo' => 'fooHelper']);
        $this->addHelper('foo', 'fooHelper2');
        $this->getHelpers()->shouldReturn(['foo' => 'fooHelper2']);
    }
}
