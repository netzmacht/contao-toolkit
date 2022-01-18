<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Formatter\Value;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ForeignKeyFormatterSpec extends ObjectBehavior
{
    public function let(Connection $database): void
    {
        $this->beConstructedWith($database);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ForeignKeyFormatter');
    }

    public function it_is_a_value_formatter(): void
    {
        $this->shouldImplement('Netzmacht\Contao\Toolkit\Dca\Formatter\Value\ValueFormatter');
    }

    public function it_accepts_foreign_key_fields(): void
    {
        $definition['foreignKey'] = 'tl_test.title';

        $this->accepts('test', $definition)->shouldReturn(true);
    }

    public function it_does_not_accept_non_foreign_key_fields(): void
    {
        $this->accepts('test', [])->shouldReturn(false);
    }

    public function it_format_by_parsing_foreign_key(Connection $database, Result $statement): void
    {
        $definition['foreignKey'] = 'tl_test.title';

        $statement->rowCount()->willReturn(1)->shouldBeCalledTimes(1);
        $statement->fetchOne()->willReturn('Title');

        $database->executeQuery(Argument::type('string'), ['id' => 5])->willReturn($statement);

        $this->format(5, 'test', $definition)->shouldReturn('Title');
    }
}
