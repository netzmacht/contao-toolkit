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

namespace spec\Netzmacht\Contao\Toolkit\Component;

use Netzmacht\Contao\Toolkit\Component\AbstractComponent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Templating\EngineInterface;
use function serialize;

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

    function let(EngineInterface $templateEngine)
    {
        $this->modelData = [
            'type'      => 'test',
            'headline'  => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'        => 1,
            'cssID'     => serialize(['', '']),
            'customTpl' => 'custom_tpl'
        ];

        $this->model = new Model($this->modelData);

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\ConcreteComponent');
        $this->beConstructedWith($this->model, $templateEngine);
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

    function it_generates_output(EngineInterface $templateEngine)
    {
        $templateEngine->render(Argument::cetera())->willReturn('output');

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
