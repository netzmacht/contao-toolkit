<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Model;

use Contao\CoreBundle\Framework\ContaoFramework;
use Doctrine\DBAL\Connection;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;
use Netzmacht\Contao\Toolkit\Data\Model\Repository;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Data\Model\ToolkitRepositoryManager;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use stdClass;

class ToolkitRepositoryManagerSpec extends ObjectBehavior
{
    public function let(Connection $connection, ContaoFramework $framework): void
    {
        $this->beConstructedWith($connection, [], $framework);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ToolkitRepositoryManager::class);
    }

    public function it_is_a_repository_manager(): void
    {
        $this->shouldImplement(RepositoryManager::class);
    }

    public function it_gets_registered_repository(
        Connection $connection,
        Repository $repository,
        ContaoFramework $framework
    ): void {
        $this->beConstructedWith($connection, ['example' => $repository], $framework);
        $this->getRepository('example')->shouldReturn($repository);
    }

    public function it_created_default_repository_for_non_registered_contao_models(ContaoFramework $framework): void
    {
        $framework->initialize()->shouldBeCalled();

        $this->getRepository(ModelExample::class)->shouldHaveType(ContaoRepository::class);
    }

    // phpcs:ignore Generic.Files.LineLength.MaxExceeded
    public function it_throws_invalid_argument_exception_when_no_repository_is_registered_nor_a_contao_model_is_passed(): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('getRepository', ['foo']);
    }

    public function it_throws_exception_if_no_repository_is_passed(
        Connection $connection,
        ContaoFramework $framework
    ): void {
        $this->beConstructedWith($connection, ['foo' => new stdClass()], $framework);
        $this->shouldThrow(AssertionFailed::class)->duringInstantiation();
    }

    public function it_has_the_database_connection(Connection $connection): void
    {
        $this->getConnection()->shouldReturn($connection);
    }
}
