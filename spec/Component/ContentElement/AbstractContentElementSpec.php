<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2020 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\Component\ContentElement;

use Netzmacht\Contao\Toolkit\Component\ContentElement\AbstractContentElement;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Templating\EngineInterface;
use Netzmacht\Contao\Toolkit\Component\Component;
use Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElement;
use function time;

if (!defined('BE_USER_LOGGED_IN')) {
    define('BE_USER_LOGGED_IN', false);
}

/**
 * Class AbstractContentElementSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Component\ContentElement
 * @mixin AbstractContentElement
 */
class AbstractContentElementSpec extends ObjectBehavior
{
    private $model;

    private $modelData;

    function let(EngineInterface $templateEngine, RequestScopeMatcher $requestScopeMatcher)
    {
        $this->modelData = [
            'type'      => 'test',
            'headline'  => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'        => 1,
            'cssID'     => serialize(['', '']),
            'customTpl' => 'custom_tpl'
        ];

        $this->model = new Model($this->modelData);

        $requestScopeMatcher->isFrontendRequest()->willReturn(true);

        $this->beAnInstanceOf(ConcreteContentElement::class);
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AbstractContentElement::class);
    }

    function it_is_a_component()
    {
        $this->shouldImplement(Component::class);
    }

    function it_is_a_content_element()
    {
        $this->shouldImplement(ContentElement::class);
    }

    function it_generates_output(EngineInterface $templateEngine)
    {
        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }

    function it_doesnt_generate_output_if_invisible(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ) {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher);

        $this->model->invisible = true;

        $templateEngine->render(Argument::cetera())->shouldNotBeCalled();

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('');
    }

    function it_doesnt_generate_output_if_starts_in_future(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ) {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher);

        $this->model->start = time() + 3600;

        $templateEngine->render(Argument::cetera())->shouldNotBeCalled();

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('');
    }

    function it_doesnt_generate_output_if_stopped_in_past(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ) {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher);

        $this->model->start = time() - 3600;
        $this->model->stop = time() - 3600;

        $templateEngine->render(Argument::cetera())->shouldNotBeCalled();

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('');
    }

    function it_ignores_visibility_settings_on_non_frontend_request(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ) {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher);

        $requestScopeMatcher->isFrontendRequest()->willReturn(false);

        $this->model->invisible = true;

        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }

    function it_ignores_visibility_settings_on_preview_mode(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ) {
        $this->beConstructedWith($this->model, $templateEngine, 'main', $requestScopeMatcher, true);

        $requestScopeMatcher->isFrontendRequest()->willReturn(true);

        $this->model->invisible = true;

        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }
}


class ConcreteContentElement extends AbstractContentElement
{

}

class Model extends \Contao\Model
{
    /**
     * Model constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->arrData = $data;
    }
}
