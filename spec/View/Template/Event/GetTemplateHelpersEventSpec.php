<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\View\Template\Event;

use Netzmacht\Contao\Toolkit\View\Template\Event\GetTemplateHelpersEvent;
use PhpSpec\ObjectBehavior;
use stdClass;

class GetTemplateHelpersEventSpec extends ObjectBehavior
{
    public const TEMPLATE_NAME = 'template_name';

    public const CONTENT_TYPE = 'content/type';

    public function let(): void
    {
        $this->beConstructedWith(self::TEMPLATE_NAME, self::CONTENT_TYPE);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(GetTemplateHelpersEvent::class);
    }

    public function it_knows_template_name(): void
    {
        $this->getTemplateName()->shouldReturn(self::TEMPLATE_NAME);
    }

    public function it_knows_content_type(): void
    {
        $this->getContentType()->shouldReturn(self::CONTENT_TYPE);
    }

    public function it_adds_helper(): void
    {
        $bar = new stdClass();
        $this->addHelper('foo', $bar)->shouldReturn($this);
        $this->getHelpers()->shouldReturn(['foo' => $bar]);
    }

    public function it_adds_helpers(): void
    {
        $foo = new stdClass();
        $bar = new stdClass();

        $helpers = [
            'foo' => $foo,
            'bar' => $bar,
        ];

        $this->addHelpers($helpers)->shouldReturn($this);
        $this->getHelpers()->shouldReturn($helpers);
    }

    public function it_only_store_latest_named_helper(): void
    {
        $foo  = new stdClass();
        $foo2 = new stdClass();

        $this->addHelper('foo', $foo);
        $this->getHelpers()->shouldReturn(['foo' => $foo]);
        $this->addHelper('foo', $foo2);
        $this->getHelpers()->shouldReturn(['foo' => $foo2]);
    }
}
