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

namespace spec\Netzmacht\Contao\Toolkit\Component\Module;

use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

if (!defined('BE_USER_LOGGED_IN')) {
    define('BE_USER_LOGGED_IN', false);
}

/**
 * Class AbstractModuleSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Component\Module
 * @mixin AbstractModule
 */
class AbstractModuleSpec extends ObjectBehavior
{
    private $model;

    private $modelData;

    function let(
        EngineInterface $templateEngine,
        TranslatorInterface $translator,
        RequestScopeMatcher $requestScopeMatcher
    ) {
        $this->modelData = [
            'type' => 'test',
            'headline' => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'   => 1,
            'cssID' => serialize(['', '']),
            'customTpl' => 'custom_tpl'
        ];

        $this->model = new Model($this->modelData);

        $requestScopeMatcher->isFrontendRequest()->willReturn(true);

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\Module\ConcreteModule');
        $this->beConstructedWith($this->model, $templateEngine, $translator, 'main', $requestScopeMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Component\Module\AbstractModule');
    }

    function it_is_a_component()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Component');
    }

    function it_is_a_content_element()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Module\Module');
    }

    function it_generates_output(EngineInterface $templateEngine, RequestScopeMatcher $requestScopeMatcher)
    {
        $requestScopeMatcher->isBackendRequest()->willReturn(false);

        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }

    function it_generates_backend_view_on_backend_request(
        EngineInterface $templateEngine,
        RequestScopeMatcher $requestScopeMatcher
    ) {
        $requestScopeMatcher->isBackendRequest()->willReturn(true);

        $templateEngine->render('toolkit:be:be_wildcard.html5', Argument::cetera())->willReturn('backend');
        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('backend');
    }
}


class ConcreteModule extends AbstractModule
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
