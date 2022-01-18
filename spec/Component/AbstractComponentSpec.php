<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Templating\EngineInterface;

use function define;
use function defined;
use function serialize;

if (! defined('TL_MODE')) {
    define('TL_MODE', 'FE');
}

class AbstractComponentSpec extends ObjectBehavior
{
    /** @var Model */
    private $model;

    /** @var array<string,mixed> */
    private $modelData;

    public function let(EngineInterface $templateEngine, RequestScopeMatcher $requestScopeMatcher): void
    {
        $this->modelData = [
            'type'      => 'test',
            'headline'  => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'        => 1,
            'cssID'     => serialize(['', '']),
            'customTpl' => 'custom_tpl',
        ];

        $this->model = new class ($this->modelData) extends Model {
            /**
             * @param array<string,mixed> $data Model data.
             */
            public function __construct(array $data)
            {
                $this->arrData = $data;
            }
        };

        $requestScopeMatcher->isFrontendRequest(Argument::any())->willReturn(true);

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\ConcreteComponent');
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Component\AbstractComponent');
    }

    public function it_is_a_component(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Component');
    }

    public function it_provides_data_read_access(): void
    {
        $this->get('id')->shouldReturn(1);
    }

    public function it_provides_data_write_access(): void
    {
        $this->set('foo', 'bar')->shouldReturn($this);
        $this->get('foo')->shouldReturn('bar');
    }

    public function it_knows_with_data_exist(): void
    {
        $this->has('id')->shouldReturn(true);
        $this->has('invalid')->shouldreturn(false);
    }

    public function it_deserializes_headline(): void
    {
        $this->get('headline')->shouldReturn('test');
        $this->get('hl')->shouldReturn('h1');
    }

    public function it_uses_custom_tpl(): void
    {
        // Only works in FE MODE!
        $this->getTemplateName()->shouldReturn('custom_tpl');
    }

    public function it_generates_output(EngineInterface $templateEngine): void
    {
        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }
}
