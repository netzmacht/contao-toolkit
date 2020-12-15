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

use Contao\Model;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;
use function serialize;

if (!defined('BE_USER_LOGGED_IN')) {
    define('BE_USER_LOGGED_IN', false);
}

/**
 * Class AbstractModuleSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Component\Module
 */
class AbstractModuleSpec extends ObjectBehavior
{
    /** @var Model */
    private $model;

    /** @var array */
    private $modelData;

    public function let(
        EngineInterface $templateEngine,
        TranslatorInterface $translator,
        RequestScopeMatcher $requestScopeMatcher
    ) {
        $this->modelData = [
            'type'      => 'test',
            'headline'  => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'        => 1,
            'cssID'     => serialize(['', '']),
            'customTpl' => 'custom_tpl'
        ];

        $this->model = new class($this->modelData) extends Model {
            /**
             * Model constructor.
             *
             * @param array $data Model data.
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

    public function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Component\Module\AbstractModule');
    }

    public function it_is_a_component()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Component');
    }

    public function it_is_a_content_element()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Module\Module');
    }

    public function it_generates_output(EngineInterface $templateEngine, RequestScopeMatcher $requestScopeMatcher)
    {
        $requestScopeMatcher->isBackendRequest()->willReturn(false);

        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }

    public function it_generates_backend_view_on_backend_request(
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
