<?php

namespace spec\Netzmacht\Contao\DevTools\Dca\Options;

use Model\Collection;
use Netzmacht\Contao\DevTools\Dca\Options\ArrayOptions;
use Netzmacht\Contao\DevTools\Dca\Options\Options;
use Netzmacht\Contao\DevTools\Dca\Options\OptionsBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class OptionsBuilderSpec
 * @package spec\Netzmacht\Contao\DevTools\Dca
 * @mixin OptionsBuilder
 */
class OptionsBuilderSpec extends ObjectBehavior
{
    function let(Options $options)
    {
        $this->beConstructedWith($options);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\DevTools\Dca\Options\OptionsBuilder');
    }

    function it_gets_the_options(Options $options)
    {
        $options->getArrayCopy()->willReturn(array());

        $this->getOptions()->shouldBeArray();
    }

    function it_converts_model_collection(Collection $collection)
    {
        $this->beConstructedThrough('fromCollection', array($collection, 'id', 'test'));
    }

    function it_groups_values_with_preserved_keys()
    {
        $data = array(
            5 => array('id' => '5', 'group' => 'a', 'label' => 'Five'),
            6 => array('id' => '6', 'group' => 'a', 'label' => 'Six'),
            7 => array('id' => '7', 'group' => 'b', 'label' => 'Seven'),
        );

        $options = new ArrayOptions($data);

        $this->beConstructedWith($options);
        $this->groupBy('group');

        $this->getOptions()->offsetExists('a')->shouldReturn(true);
        $this->getOptions()->offsetGet('a')->shouldReturn(array(
            5 => array('id' => '5', 'group' => 'a', 'label' => 'Five'),
            6 => array('id' => '6', 'group' => 'a', 'label' => 'Six'),
        ));

        $this->getOptions()->offsetExists('b')->shouldReturn(true);
        $this->getOptions()->offsetGet('b')->shouldReturn(array(
            7 => array('id' => '7', 'group' => 'b', 'label' => 'Seven'),
        ));
    }

    function it_groups_values_by_callback()
    {
        $data = array(
            5 => array('id' => '5', 'group' => 'a', 'label' => 'Five'),
            6 => array('id' => '6', 'group' => 'a', 'label' => 'Six'),
            7 => array('id' => '7', 'group' => 'b', 'label' => 'Seven'),
        );

        $options  = new ArrayOptions($data);
        $callback = function ($group) {
            return $group . $group;
        };

        $this->beConstructedWith($options);
        $this->groupBy('group', $callback);

        $this->getOptions()->offsetExists('aa')->shouldReturn(true);
        $this->getOptions()->offsetGet('aa')->shouldReturn(array(
            5 => array('id' => '5', 'group' => 'a', 'label' => 'Five'),
            6 => array('id' => '6', 'group' => 'a', 'label' => 'Six'),
        ));

        $this->getOptions()->offsetExists('bb')->shouldReturn(true);
        $this->getOptions()->offsetGet('bb')->shouldReturn(array(
            7 => array('id' => '7', 'group' => 'b', 'label' => 'Seven'),
        ));
    }
}
