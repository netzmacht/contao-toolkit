<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Component\ContentElement;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ContentElement\AbstractContentElement;
use Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElement;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

use function define;
use function defined;
use function serialize;
use function time;

if (! defined('BE_USER_LOGGED_IN')) {
    define('BE_USER_LOGGED_IN', false);
}

class AbstractContentElementSpec extends ObjectBehavior
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

        $requestScopeMatcher->isFrontendRequest()->willReturn(true);

        $this->beAnInstanceOf(\spec\Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElement::class);
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher, false);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractContentElement::class);
    }

    public function it_is_a_component(): void
    {
        $this->shouldImplement(Component::class);
    }

    public function it_is_a_content_element(): void
    {
        $this->shouldImplement(ContentElement::class);
    }

    public function it_generates_output(EngineInterface $templateEngine): void
    {
        $templateEngine
            ->render(Argument::type(TemplateReferenceInterface::class), Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn('output');

        $this->generate()->shouldReturn('output');
    }

    public function it_doesnt_generate_output_if_invisible(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ): void {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher, false);

        $this->model->invisible = true;

        $templateEngine->render(Argument::cetera())->shouldNotBeCalled();

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('');
    }

    public function it_doesnt_generate_output_if_starts_in_future(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ): void {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher, false);

        $this->model->start = time() + 3600;

        $templateEngine->render(Argument::cetera())->shouldNotBeCalled();

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('');
    }

    public function it_doesnt_generate_output_if_stopped_in_past(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ): void {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher, false);

        $this->model->start = time() - 3600;
        $this->model->stop  = time() - 3600;

        $templateEngine->render(Argument::cetera())->shouldNotBeCalled();

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('');
    }

    public function it_ignores_visibility_settings_on_non_frontend_request(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ): void {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher, false);

        $requestScopeMatcher->isFrontendRequest()->willReturn(false);

        $this->model->invisible = true;

        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }

    public function it_ignores_visibility_settings_on_preview_mode(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ): void {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher, true);

        $requestScopeMatcher->isFrontendRequest()->willReturn(true);

        $this->model->invisible = true;

        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }
}
