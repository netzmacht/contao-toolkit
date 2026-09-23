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

    public function it_builds_a_tree_from_array_list_with_ids_differing_from_positions(): void
    {
        $data = [
            ['id' => 5, 'pid' => 0, 'title' => 'Root'],
            ['id' => 7, 'pid' => 5, 'title' => 'Child'],
            ['id' => 9, 'pid' => 7, 'title' => 'Grandchild'],
            ['id' => 3, 'pid' => 0, 'title' => 'Second root'],
        ];

        $this->beConstructedWith(new ArrayListOptions($data, 'title', 'id'));
        $this->asTree();

        $this->getOptions()->shouldReturn(
            [
                5 => ' Root',
                7 => '--  Child',
                9 => '-- --  Grandchild',
                3 => ' Second root',
            ],
        );
    }

    public function it_builds_a_tree_with_custom_parent_column_and_indent(): void
    {
        $data = [
            ['id' => 1, 'parent' => 0, 'title' => 'Root'],
            ['id' => 2, 'parent' => 1, 'title' => 'Child'],
        ];

        $this->beConstructedWith(new ArrayListOptions($data, 'title', 'id'));
        $this->asTree('parent', '>');

        $this->getOptions()->shouldReturn([1 => ' Root', 2 => '> Child']);
    }
}
