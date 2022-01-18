<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Options;

use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Dca\Options\ArrayListOptions;
use Netzmacht\Contao\Toolkit\Dca\Options\Options;
use PhpSpec\ObjectBehavior;

use function expect;

class OptionsBuilderSpec extends ObjectBehavior
{
    public function let(Options $options): void
    {
        $this->beConstructedWith($options);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Options\OptionsBuilder');
    }

    public function it_gets_the_options(Options $options): void
    {
        $options->getArrayCopy()->willReturn([]);

        $this->getOptions()->shouldBeArray();
    }

    public function it_converts_model_collection(Collection $collection): void
    {
        $this->beConstructedThrough('fromCollection', [$collection, 'id', 'test']);
    }

    public function it_groups_values_with_preserved_keys(): void
    {
        $data = [
            5 => ['id' => '5', 'group' => 'a', 'label' => 'Five'],
            6 => ['id' => '6', 'group' => 'a', 'label' => 'Six'],
            7 => ['id' => '7', 'group' => 'b', 'label' => 'Seven'],
        ];

        $options = new ArrayListOptions($data, 'id', 'label');

        $this->beConstructedWith($options);
        $this->groupBy('group');
    }

    public function it_groups_values_by_callback(): void
    {
        $data = [
            5 => ['id' => '5', 'group' => 'a', 'label' => 'Five'],
            6 => ['id' => '6', 'group' => 'a', 'label' => 'Six'],
            7 => ['id' => '7', 'group' => 'b', 'label' => 'Seven'],
        ];

        $options  = new ArrayListOptions($data, 'id', 'label');
        $callback = static function ($group) {
            return $group . $group;
        };

        $this->beConstructedWith($options);
        $this->groupBy('group', $callback);

        expect($this->getOptions()->offsetExists('aa'))->shouldReturn(true);
    }
}
