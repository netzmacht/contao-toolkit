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

namespace spec\Netzmacht\Contao\Toolkit\Dca\Options;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Dca\Options\ArrayListOptions;
use Netzmacht\Contao\Toolkit\Dca\Options\Options;
use PhpSpec\ObjectBehavior;

/**
 * Class OptionsBuilderSpec
 */
class OptionsBuilderSpec extends ObjectBehavior
{
    public function let(Options $options)
    {
        $this->beConstructedWith($options);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder');
    }

    public function it_gets_the_options(Options $options)
    {
        $options->getArrayCopy()->willReturn(array());

        $this->getOptions()->shouldBeArray();
    }

    public function it_converts_model_collection(Collection $collection)
    {
        $this->beConstructedThrough('fromCollection', array($collection, 'id', 'test'));
    }

    public function it_groups_values_with_preserved_keys()
    {
        $data = array(
            5 => array('id' => '5', 'group' => 'a', 'label' => 'Five'),
            6 => array('id' => '6', 'group' => 'a', 'label' => 'Six'),
            7 => array('id' => '7', 'group' => 'b', 'label' => 'Seven'),
        );

        $options = new ArrayListOptions($data, 'id', 'label');

        $this->beConstructedWith($options);
        $this->groupBy('group');
    }

    public function it_groups_values_by_callback()
    {
        $data = array(
            5 => array('id' => '5', 'group' => 'a', 'label' => 'Five'),
            6 => array('id' => '6', 'group' => 'a', 'label' => 'Six'),
            7 => array('id' => '7', 'group' => 'b', 'label' => 'Seven'),
        );

        $options  = new ArrayListOptions($data, 'id', 'label');
        $callback = function ($group) {
            return $group . $group;
        };

        $this->beConstructedWith($options);
        $this->groupBy('group', $callback);

        $this->getOptions()->offsetExists('aa')->shouldReturn(true);
    }
}
