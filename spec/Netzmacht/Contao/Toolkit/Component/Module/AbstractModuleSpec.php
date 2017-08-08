<?php

namespace spec\Netzmacht\Contao\Toolkit\Component\Module;

use ContaoCommunityAlliance\Translator\TranslatorInterface;
use Netzmacht\Contao\Toolkit\Component\Module\AbstractModule;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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

    function let(TemplateFactory $templateFactory, Template $template, TranslatorInterface $translator)
    {
        $this->modelData = [
            'type' => 'test',
            'headline' => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'   => 1,
            'customTpl' => 'custom_tpl'
        ];

        $this->model = new Model($this->modelData);

        $templateFactory->createFrontendTemplate(Argument::cetera())->willReturn($template);

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\Module\ConcreteModule');
        $this->beConstructedWith($this->model, $templateFactory, $translator);
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

    function it_generates_output(Template $template)
    {
        $template->set(Argument::type('string'), Argument::any())->shouldBeCalled();
        $template->parse()->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
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
