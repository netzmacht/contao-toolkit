<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Validator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\Result;
use Doctrine\DBAL\Query\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UniqueDatabaseValueValidatorSpec extends ObjectBehavior
{
    public const TABLE_NAME = 'table';

    public const FIELD_NAME = 'alias';

    public function let(Connection $connection): void
    {
        $this->beConstructedWith($connection, self::TABLE_NAME, self::FIELD_NAME);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator');
    }

    public function it_is_a_validator(): void
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Validator');
    }

    public function it_validates_when_value_not_exists(
        Connection $connection,
        QueryBuilder $queryBuilder,
        Result $statement
    ): void {
        $result = (object) ['result' => 0];

        $connection->createQueryBuilder()->willReturn($queryBuilder);

        $queryBuilder->select(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->from(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->where(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder
            ->setParameter(Argument::type('string'), Argument::any())
            ->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->execute()->willReturn($statement);

        $statement->fetchOne()->willReturn(0);

        $this->validate($result, 'foo')->shouldReturn(true);
    }

    public function it_invalidates_when_value_exists(
        Connection $connection,
        QueryBuilder $queryBuilder,
        Result $statement
    ): void {
        $result = (object) ['result' => 1];

        $connection->createQueryBuilder()->willReturn($queryBuilder);

        $queryBuilder->select(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->from(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->where(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder
            ->setParameter(Argument::type('string'), Argument::any())
            ->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->execute()->willReturn($statement);

        $statement->fetchOne()->willReturn(1);

        $this->validate($result, 'foo')->shouldReturn(false);
    }
}
