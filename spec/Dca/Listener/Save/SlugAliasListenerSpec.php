<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Dca\Listener\Save;

use Contao\CoreBundle\Slug\Slug;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Dca\Listener\Save\SlugAliasListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Netzmacht\Contao\Toolkit\DataContainerSpecHelper;
use stdClass;

class SlugAliasListenerSpec extends ObjectBehavior
{
    use DataContainerSpecHelper;

    public function let(Slug $slug, Connection $connection, DcaManager $dcaManager): void
    {
        $this->beConstructedWith($slug, $connection, $dcaManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(SlugAliasListener::class);
    }

    public function it_generates_an_alias_using_the_configured_fields(
        Slug $slug,
        Connection $connection,
        DcaManager $dcaManager,
        QueryBuilder $queryBuilder,
    ): void {
        $dca = [
            'fields' => [
                'alias' => ['toolkit' => ['alias_generator' => ['fields' => ['title']]]],
            ],
        ];

        $dcaManager->getDefinition('tl_example')->willReturn(new Definition('tl_example', $dca));

        $activeRecord        = new stdClass();
        $activeRecord->id    = 1;
        $activeRecord->title = 'Hello World';

        $dataContainer = $this->createDataContainer('tl_example', 'alias', null, $activeRecord);

        // UniqueDatabaseValueValidator::validate() builds: select(...)->from($table)->where($col.'= :value')
        // ->setParameter('value', $value)->andWhere('id NOT IN(:excluded)')->setParameter('excluded', [1], ...)
        // and reads the result via the QueryBuilder's own fetchOne() (not ->execute()).
        $connection->createQueryBuilder()->willReturn($queryBuilder);
        $queryBuilder->select(Argument::type('string'))->willReturn($queryBuilder);
        $queryBuilder->from('tl_example')->willReturn($queryBuilder);
        $queryBuilder->where('alias= :value')->willReturn($queryBuilder);
        $queryBuilder->andWhere('id NOT IN(:excluded)')->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->fetchOne()->willReturn(0);

        $slug
            ->generate('Hello World', ['delimiter' => '-'], Argument::type('callable'))
            ->willReturn('hello-world');

        $this->onSaveCallback(null, $dataContainer)->shouldReturn('hello-world');
    }
}
