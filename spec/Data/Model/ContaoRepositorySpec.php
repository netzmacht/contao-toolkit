<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Data\Model\QuerySpecification;
use Netzmacht\Contao\Toolkit\Data\Model\Specification;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use function get_class;

final class ContaoRepositorySpec extends ObjectBehavior
{
    private Model $modelInstance;

    /** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
    public function let(): void
    {
        $this->modelInstance = new class extends Model{
            /** @var string */
            // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
            protected static $strTable = 'tl_spec';

            public function __construct()
            {
            }

            /** {@inheritDoc} */
            protected static function find(array $arrOptions)
            {
                return null;
            }

            /** {@inheritDoc} */
            public static function countBy($strColumn = null, $varValue = null, array $arrOptions = [])
            {
                return 0;
            }
        };

        $this->beConstructedWith(get_class($this->modelInstance));
    }

    public function it_finds_by_specification(Specification $specification): void
    {
        $this->findBySpecification($specification);

        $specification
            ->buildQuery(Argument::type('array'), Argument::type('array'))
            ->shouldBeCalledOnce();
    }

    public function it_finds_by_query_specification(QuerySpecification $specification): void
    {
        $this->findBySpecification($specification);

        $specification
            ->buildQueryWithOptions(Argument::type('array'), Argument::type('array'), Argument::type('array'))
            ->shouldBeCalledOnce();
    }

    public function it_counts_by_specification(Specification $specification): void
    {
        $this->countBySpecification($specification);

        $specification
            ->buildQuery(Argument::type('array'), Argument::type('array'))
            ->shouldBeCalledOnce();
    }

    public function it_counts_by_query_specification(QuerySpecification $specification): void
    {
        $this->countBySpecification($specification);

        $specification
            ->buildQuery(Argument::type('array'), Argument::type('array'))
            ->shouldBeCalledOnce();
    }
}
