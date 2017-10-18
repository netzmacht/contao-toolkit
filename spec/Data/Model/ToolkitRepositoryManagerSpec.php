<?php

namespace spec\Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\AssertionFailed;
use Netzmacht\Contao\Toolkit\Data\Model\ContaoRepository;
use Netzmacht\Contao\Toolkit\Data\Model\Repository;
use Netzmacht\Contao\Toolkit\Data\Model\RepositoryManager;
use Netzmacht\Contao\Toolkit\Data\Model\ToolkitRepositoryManager;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use PhpSpec\ObjectBehavior;

class ToolkitRepositoryManagerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith([]);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType(ToolkitRepositoryManager::class);
    }

    function it_is_a_repository_manager()
    {
        $this->shouldImplement(RepositoryManager::class);
    }

    function it_gets_registered_repository(Repository $repository)
    {
        $this->beConstructedWith(['example' => $repository]);
        $this->getRepository('example')->shouldReturn($repository);
    }

    function it_created_default_repository_for_non_registered_contao_models()
    {
        $this->getRepository(SampleModel::class)->shouldHaveType(ContaoRepository::class);
    }

    function it_throws_invalid_argument_exception_when_no_repository_is_registered_nor_a_contao_model_is_passed()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('getRepository', ['foo']);
    }

    function it_throws_exception_if_no_repository_is_passed()
    {
        $this->beConstructedWith(['foo' => new \stdClass()]);
        $this->shouldThrow(AssertionFailed::class)->duringInstantiation();
    }
}

class SampleModel extends Model
{

}
