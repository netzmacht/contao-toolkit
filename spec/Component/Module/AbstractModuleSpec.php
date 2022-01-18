<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Component\Module;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function define;
use function defined;
use function serialize;

if (! defined('BE_USER_LOGGED_IN')) {
    define('BE_USER_LOGGED_IN', false);
}

class AbstractModuleSpec extends ObjectBehavior
{
    /** @var Model */
    private $model;

    /** @var array<string,mixed> */
    private $modelData;

    public function let(
        EngineInterface $templateEngine,
        TranslatorInterface $translator,
        RequestScopeMatcher $requestScopeMatcher
    ): void {
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

        $this->beAnInstanceOf(FrontendModule::class);
        $this->beConstructedWith($this->model, $templateEngine, $translator, 'main', $requestScopeMatcher);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Component\Module\AbstractModule');
    }

    public function it_is_a_component(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Component');
    }

    public function it_is_a_content_element(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Module\Module');
    }

    public function it_generates_output(EngineInterface $templateEngine, RequestScopeMatcher $requestScopeMatcher): void
    {
        $requestScopeMatcher->isBackendRequest()->willReturn(false);

        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }

    public function it_generates_backend_view_on_backend_request(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ): void {
        $requestScopeMatcher->isBackendRequest()->willReturn(true);

        $templateEngine->render('toolkit:be:be_wildcard.html5', Argument::cetera())->willReturn('backend');
        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('backend');
    }
}
