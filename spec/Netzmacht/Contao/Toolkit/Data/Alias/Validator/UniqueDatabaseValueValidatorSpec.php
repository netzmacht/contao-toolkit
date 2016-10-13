<?php

namespace spec\Netzmacht\Contao\Toolkit\Data\Alias\Validator;

use Database;
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

    function let(Database $database)
    {
        $this->beConstructedWith($database, static::TABLE_NAME, static::FIELD_NAME);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Validator\UniqueDatabaseValueValidator');
    }

    function it_is_a_validator()
    {
        $this->shouldHaveType('Netzmacht\Contao\Toolkit\Data\Alias\Validator');
    }

    function it_validates_when_value_not_exists(Database $database, Database\Statement $statement)
    {
        $result = (object) ['result' => 0];
        $database->prepare(Argument::any())->willReturn($statement);
        $statement->execute(['foo'])->willReturn($result);

        $this->validate($result, 'foo')->shouldReturn(true);
    }

    function it_invalidates_when_value_exists(Database $database, Database\Statement $statement)
    {
        $result = (object) ['result' => 1];
        $database->prepare(Argument::any())->willReturn($statement);
        $statement->execute(['foo'])->willReturn($result);

        $this->validate($result, 'foo')->shouldReturn(false);
    }
}
