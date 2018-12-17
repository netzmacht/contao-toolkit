<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2018 netzmacht David Molineus.
 * @license    LGPL-3.0-or-later https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

namespace spec\Netzmacht\Contao\Toolkit\Component\ContentElement;

use Netzmacht\Contao\Toolkit\Component\ContentElement\AbstractContentElement;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Templating\EngineInterface;

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

    function let(EngineInterface $templateEngine)
    {
        $this->modelData = [
            'type' => 'test',
            'headline' => serialize(['unit' => 'h1', 'value' => 'test']),
            'id'   => 1,
            'customTpl' => 'custom_tpl'
        ];

        $this->model = new Model($this->modelData);

        $this->beAnInstanceOf('spec\Netzmacht\Contao\Toolkit\Component\ContentElement\ConcreteContentElement');
        $this->beConstructedWith($this->model, $templateEngine);
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

    function it_generates_output(EngineInterface $templateEngine)
    {
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
