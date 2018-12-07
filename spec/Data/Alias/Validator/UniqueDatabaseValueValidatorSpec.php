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

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Validator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;
use Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class UniqueDatabaseValueValidatorSpec
 *
 * @package spec\Netzmacht\Contao\Toolkit\Data\Alias\Validator
 * @mixin UniqueDatabaseValueValidator
 */
class UniqueDatabaseValueValidatorSpec extends ObjectBehavior
{
    const TABLE_NAME = 'table';

    const FIELD_NAME = 'alias';

    function let(Connection $connection)
    {
        $this->beConstructedWith($connection, static::TABLE_NAME, static::FIELD_NAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator');
    }

    function it_is_a_validator()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Validator');
    }

    function it_validates_when_value_not_exists(
        Connection $connection,
        QueryBuilder $queryBuilder,
        Statement $statement
    ) {
        $result = (object) ['result' => 0];

        $connection->createQueryBuilder()->willReturn($queryBuilder);

        $queryBuilder->select(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->from(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->where(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->setParameter(Argument::type('string'), Argument::any())->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->execute()->willReturn($statement);

        $statement->fetch()->willReturn(['result' => 0]);

        $this->validate($result, 'foo')->shouldReturn(true);
    }

    function it_invalidates_when_value_exists(Connection $connection, QueryBuilder $queryBuilder, Statement $statement)
    {
        $result = (object) ['result' => 1];

        $connection->createQueryBuilder()->willReturn($queryBuilder);

        $queryBuilder->select(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->from(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->where(Argument::type('string'))->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->setParameter(Argument::type('string'), Argument::any())->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->execute()->willReturn($statement);

        $statement->fetch()->willReturn(['result' => 1]);

        $this->validate($result, 'foo')->shouldReturn(false);
    }
}
