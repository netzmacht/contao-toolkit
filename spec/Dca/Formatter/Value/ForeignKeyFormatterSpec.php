<?php

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ForeignKeyFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ForeignKeyFormatterSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value
 * @mixin ForeignKeyFormatter
 */
class ForeignKeyFormatterSpec extends ObjectBehavior
{
    function let(Connection $database)
    {
        $this->beConstructedWith($database);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ForeignKeyFormatter');
    }

    function it_is_a_value_formatter()
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    function it_accepts_foreign_key_fields()
    {
        $definition['foreignKey'] = 'tl_test.title';

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    function it_does_not_accept_non_foreign_key_fields()
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    function it_format_by_parsing_foreign_key(Connection $database, Statement $statement)
    {
        $definition['foreignKey'] = 'tl_test.title';

        $result = (object) [
            'value'   => 'Title',
            'numRows' => 1
        ];

        $statement->bindValue('id', 5)->shouldBeCalled();
        $statement->rowCount()->willReturn(1)->shouldBeCalledTimes(1);

        $database->prepare(Argument::any())->willReturn($statement);
        $statement->execute()->willReturn(true);
        $statement->fetchColumn(0)->willReturn('Title');

        $this->format(5, 'test', $definition)->shouldReturn('Title');
    }
}
