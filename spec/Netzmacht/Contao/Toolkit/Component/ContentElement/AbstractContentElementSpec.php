<?php

namespace spec\Netzmacht\Contao\Toolkit\Component\ContentElement;

use Netzmacht\Contao\Toolkit\Component\ContentElement\AbstractContentElement;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

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

    function let(TemplateFactory $templateFactory, Template $template)
    {
        $this->modelData = [
            'type' => 'test',
            'headline' => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'   => 1,
            'customTpl' => 'custom_tpl'
        ];

        $this->model = new Model($this->modelData);

        $templateFactory->createFrontendTemplate(Argument::cetera())->willReturn($template);

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\ContentElement\ConcreteContentElement');
        $this->beConstructedWith($this->model, $templateFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Component\ContentElement\AbstractContentElement');
    }

    function it_is_a_component()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Component');
    }

    function it_is_a_content_element()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\ContentElement\ContentElement');
    }

    function it_generates_output(Template $template)
    {
        $template->set(Argument::type('string'), Argument::any())->shouldBeCalled();
        $template->parse()->willReturn('output');

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
