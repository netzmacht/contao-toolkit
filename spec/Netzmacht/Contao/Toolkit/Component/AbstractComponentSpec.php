<?php

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use Netzmacht\Contao\Toolkit\View\Template;
use Netzmacht\Contao\Toolkit\View\Template\TemplateFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

if (!defined('TL_MODE')) {
    define('TL_MODE', 'FE');
}

/**
 * Class AbstractComponentSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Component
 * @mixin AbstractComponent
 */
class AbstractComponentSpec extends ObjectBehavior
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

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\ConcreteComponent');
        $this->beConstructedWith($this->model, $templateFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Component\AbstractComponent');
    }

    function it_is_a_component()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Component');
    }

    function it_provides_data_read_access()
    {
        $this->get('id')->shouldReturn(1);
    }

    function it_provides_data_write_access()
    {
        $this->set('foo', 'bar')->shouldReturn($this);
        $this->get('foo')->shouldReturn('bar');
    }

    function it_knows_with_data_exist()
    {
        $this->has('id')->shouldReturn(true);
        $this->has('invalid')->shouldreturn(false);
    }

    function it_deserializes_headline()
    {
        $this->get('headline')->shouldReturn('test');
        $this->get('hl')->shouldReturn('h1');
    }

    function it_uses_custom_tpl()
    {
        // Only works in FE MODE!
        $this->getTemplateName()->shouldReturn('custom_tpl');
    }

    function it_generates_output(Template $template)
    {
        $template->set(Argument::type('string'), Argument::any())->shouldBeCalled();
        $template->parse()->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }
}

class ConcreteComponent extends AbstractComponent
{
    public function getTemplateName(): string
    {
        return parent::getTemplateName();
    }

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
