<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\InsertTag;

use Netzmacht\Contao\Toolkit\InsertTag\AbstractInsertTagParser;
use PhpSpec\ObjectBehavior;
use spec\Netzmacht\Contao\Toolkit\DeprecationSpecHelper;

class AbstractInsertTagParserSpec extends ObjectBehavior
{
    use DeprecationSpecHelper;

    public function let(): void
    {
        $this->beAnInstanceOf(ConcreteInsertTagParser::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractInsertTagParser::class);
    }

    public function it_triggers_a_deprecation_warning_when_instantiated(): void
    {
        $messages = $this->captureDeprecations(function (): void {
            $this->getWrappedObject();
        });

        $this->assertDeprecationTriggered($messages, 'InsertTag component');
    }

    public function it_still_replaces_a_supported_tag(): void
    {
        $this->replace('foo::bar')->shouldReturn('parsed:foo');
    }

    public function it_still_returns_false_for_an_unsupported_tag(): void
    {
        $this->replace('unsupported::bar')->shouldReturn(false);
    }
}
