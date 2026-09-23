<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter;

use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Event\CreateFormatterEvent;
use Netzmacht\Contao\Toolkit\Dca\Formatter\FormatterFactory;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class FormatterFactorySpec extends ObjectBehavior
{
    public function let(EventDispatcherInterface $eventDispatcher): void
    {
        $this->beConstructedWith($eventDispatcher);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FormatterFactory::class);
    }

    public function it_applies_post_filters_after_formatter_without_pre_filters(
        EventDispatcherInterface $eventDispatcher,
        ValueFormatter $formatter,
        ValueFormatter $postFilter,
        ValueFormatter $optionsFormatter,
    ): void {
        $dca        = ['fields' => ['title' => ['inputType' => 'text']]];
        $definition = new Definition('tl_example', $dca);

        $formatter->accepts('title', Argument::type('array'))->willReturn(true);
        $formatter->format('foo', 'title', Argument::type('array'), null)->willReturn('bar');

        $postFilter->accepts('title', Argument::type('array'))->willReturn(true);
        $postFilter->format('bar', 'title', Argument::type('array'), null)->willReturn('baz');

        $eventDispatcher
            ->dispatch(Argument::type(CreateFormatterEvent::class), CreateFormatterEvent::NAME)
            ->will(
                static function (array $arguments) use (
                    $formatter,
                    $postFilter,
                    $optionsFormatter,
                ): CreateFormatterEvent {
                    $event = $arguments[0];
                    $event->addFormatter($formatter->getWrappedObject());
                    $event->addPostFilter($postFilter->getWrappedObject());
                    $event->setOptionsFormatter($optionsFormatter->getWrappedObject());

                    return $event;
                },
            );

        $this->createFormatterFor($definition)->formatValue('title', 'foo')->shouldReturn('baz');
    }
}
