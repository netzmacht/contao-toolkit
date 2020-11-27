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

use Contao\Model;
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
 */
class AbstractComponentSpec extends ObjectBehavior
{
    /** @var \Contao\Model */
    private $model;

    /** @var array */
    private $modelData;

    public function let(EngineInterface $templateEngine)
    {
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

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\ConcreteComponent');
        $this->beConstructedWith($this->model, $templateEngine);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Component\AbstractComponent');
    }

    public function it_is_a_component()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Component\Component');
    }

    public function it_provides_data_read_access()
    {
        $this->get('id')->shouldReturn(1);
    }

    public function it_provides_data_write_access()
    {
        $this->set('foo', 'bar')->shouldReturn($this);
        $this->get('foo')->shouldReturn('bar');
    }

    public function it_knows_with_data_exist()
    {
        $this->has('id')->shouldReturn(true);
        $this->has('invalid')->shouldreturn(false);
    }

    public function it_deserializes_headline()
    {
        $this->get('headline')->shouldReturn('test');
        $this->get('hl')->shouldReturn('h1');
    }

    public function it_uses_custom_tpl()
    {
        // Only works in FE MODE!
        $this->getTemplateName()->shouldReturn('custom_tpl');
    }

    public function it_generates_output(EngineInterface $templateEngine)
    {
        $templateEngine->render(Argument::cetera())->willReturn('output');

        $this->generate()->shouldBeString();
        $this->generate()->shouldReturn('output');
    }
}
