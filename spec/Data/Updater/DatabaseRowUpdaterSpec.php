<?php

declare(strict_types=1);

namespace spec\Netzmacht\Contao\Toolkit\Data\Updater;

use Contao\CoreBundle\Cache\CacheTagManager;
use Contao\CoreBundle\DataContainer\VirtualFieldsHandler;
use Contao\CoreBundle\Framework\Adapter;
use Contao\CoreBundle\Security\DataContainer\ReadAction;
use Contao\CoreBundle\Security\DataContainer\UpdateAction;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use Netzmacht\Contao\Toolkit\Callback\Invoker;
use Netzmacht\Contao\Toolkit\Data\Updater\DatabaseRowUpdater;
use Netzmacht\Contao\Toolkit\Dca\DcaManager;
use Netzmacht\Contao\Toolkit\Dca\Definition;
use Netzmacht\Contao\Toolkit\Exception\AccessDenied;
use Netzmacht\Contao\Toolkit\Exception\InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\SecurityBundle\Security;

/** @SuppressWarnings(PHPMD.TooManyPublicMethods) */
final class DatabaseRowUpdaterSpec extends ObjectBehavior
{
    private const RECORD = [
        'id'        => 1,
        'pid'       => 3,
        'tstamp'    => 100,
        'title'     => 'Old',
        'published' => '',
        'password'  => 'secret',
        'sorting'   => 32,
    ];

    /** @var array<string,mixed> */
    private array $dca;

    public function let(
        Security $security,
        Connection $connection,
        DcaManager $dcaManager,
        Adapter $systemAdapter,
        VirtualFieldsHandler $virtualFieldsHandler,
        CacheTagManager $cacheTagManager,
        QueryBuilder $queryBuilder,
        AbstractPlatform $platform,
    ): void {
        $this->dca = [
            'config' => ['dataContainer' => 'Table', 'ptable' => 'tl_parent'],
            'list'   => ['sorting' => ['mode' => 4]],
            'fields' => [
                'id'        => ['sql' => ['type' => 'integer']],
                'pid'       => ['sql' => ['type' => 'integer']],
                'tstamp'    => ['sql' => ['type' => 'integer']],
                'title'     => ['inputType' => 'text', 'exclude' => false, 'sql' => ['type' => 'string']],
                'published' => ['inputType' => 'checkbox', 'sql' => ['type' => 'boolean']],
                'password'  => [
                    'inputType' => 'password',
                    'exclude'   => false,
                    'eval'      => ['doNotSaveEmpty' => true],
                    'sql'       => ['type' => 'string'],
                ],
                'sorting'   => ['exclude' => false, 'sql' => ['type' => 'integer']],
            ],
        ];

        // Pass the dca by reference, so that examples can modify it.
        $dca = &$this->dca;
        $dcaManager->getDefinition('tl_example')->will(
            static function () use (&$dca): Definition {
                return new Definition('tl_example', $dca);
            },
        );

        $security->isGranted(Argument::cetera())->willReturn(true);

        $platform->quoteSingleIdentifier(Argument::type('string'))->willReturnArgument(0);
        $connection->getDatabasePlatform()->willReturn($platform);
        $connection->fetchAssociative('SELECT * FROM tl_example WHERE id=?', [1])->willReturn(self::RECORD);
        $connection->createQueryBuilder()->willReturn($queryBuilder);

        $queryBuilder->update(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->where(Argument::any())->willReturn($queryBuilder);
        $queryBuilder->set(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->setParameter(Argument::cetera())->willReturn($queryBuilder);
        $queryBuilder->executeStatement()->willReturn(1);

        $virtualFieldsHandler->expandFields(Argument::type('array'), 'tl_example')->willReturnArgument(0);
        $virtualFieldsHandler->combineFields(Argument::type('array'), 'tl_example')->willReturnArgument(0);

        $cacheTagManager->invalidateTags(Argument::type('array'))->willReturn($cacheTagManager);

        $this->beConstructedWith(
            $security,
            $connection,
            $dcaManager,
            new Invoker($systemAdapter->getWrappedObject()),
            $virtualFieldsHandler,
            $cacheTagManager,
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DatabaseRowUpdater::class);
    }

    public function it_grants_access_to_not_excluded_fields(Security $security): void
    {
        $security->isGranted('contao_user.alexf', 'tl_example::title')->shouldNotBeCalled();

        $this->hasUserAccess('tl_example', 'title')->shouldReturn(true);
    }

    public function it_grants_access_to_excluded_fields_when_the_security_voter_grants_it(Security $security): void
    {
        $security->isGranted('contao_user.alexf', 'tl_example::published')->willReturn(true);

        $this->hasUserAccess('tl_example', 'published')->shouldReturn(true);
    }

    public function it_denies_access_to_excluded_fields_when_the_security_voter_denies_it(Security $security): void
    {
        $security->isGranted('contao_user.alexf', 'tl_example::published')->willReturn(false);

        $this->hasUserAccess('tl_example', 'published')->shouldReturn(false);
    }

    public function it_updates_changed_values_and_sets_the_tstamp(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->update('tl_example')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('id', 1)->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->set('title', ':title')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('title', 'New', 'string')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->set('tstamp', ':tstamp')->shouldBeCalled()->willReturn($queryBuilder);

        $values = $this->update('tl_example', 1, ['title' => 'New'], null);
        $values->shouldHaveKeyWithValue('title', 'New');
        $values->shouldHaveKey('tstamp');
    }

    public function it_throws_if_table_is_not_editable(): void
    {
        $this->dca['config']['notEditable'] = true;

        $this->shouldThrow(AccessDenied::class)->during('update', ['tl_example', 1, ['title' => 'New'], null]);
    }

    public function it_throws_if_record_does_not_exist(Connection $connection): void
    {
        $connection->fetchAssociative('SELECT * FROM tl_example WHERE id=?', [2])->willReturn(false);

        $this->shouldThrow(AccessDenied::class)->during('update', ['tl_example', 2, ['title' => 'New'], null]);
    }

    public function it_throws_if_user_has_no_access_to_excluded_field(Security $security): void
    {
        $security->isGranted('contao_user.alexf', 'tl_example::published')->willReturn(false);

        $this->shouldThrow(AccessDenied::class)->during('update', ['tl_example', 1, ['published' => '1'], null]);
    }

    public function it_throws_if_user_cannot_read_the_record(Security $security): void
    {
        $security->isGranted('contao_dc.tl_example', Argument::type(ReadAction::class))->willReturn(false);

        $this->shouldThrow(AccessDenied::class)->during('update', ['tl_example', 1, ['title' => 'New'], null]);
    }

    public function it_throws_if_update_action_is_denied(Security $security, QueryBuilder $queryBuilder): void
    {
        $security->isGranted('contao_dc.tl_example', Argument::type(UpdateAction::class))->willReturn(false);
        $queryBuilder->executeStatement()->shouldNotBeCalled();

        $this->shouldThrow(AccessDenied::class)->during('update', ['tl_example', 1, ['title' => 'New'], null]);
    }

    public function it_skips_empty_values_of_do_not_save_empty_fields(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->set('password', Argument::any())->shouldNotBeCalled();
        $queryBuilder->executeStatement()->shouldNotBeCalled();

        $this->update('tl_example', 1, ['password' => ''], null)->shouldReturn([]);
    }

    public function it_skips_unchanged_values(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->executeStatement()->shouldNotBeCalled();

        $this->update('tl_example', 1, ['title' => 'Old'], null)->shouldReturn([]);
    }

    public function it_saves_unchanged_values_of_always_save_fields(QueryBuilder $queryBuilder): void
    {
        $this->dca['fields']['title']['eval']['alwaysSave'] = true;

        $queryBuilder->setParameter('title', 'Old', 'string')->shouldBeCalled()->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['title' => 'Old'], null)->shouldHaveKeyWithValue('title', 'Old');
    }

    public function it_converts_empty_values_by_field_type(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->setParameter('sorting', 0, 'integer')->shouldBeCalled()->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['sorting' => ''], null)->shouldHaveKeyWithValue('sorting', 0);
    }

    public function it_converts_empty_values_of_nullable_fields_to_null(QueryBuilder $queryBuilder): void
    {
        $this->dca['fields']['title']['sql'] = ['type' => 'string', 'notnull' => false];

        $queryBuilder->setParameter('title', null, 'string')->shouldBeCalled()->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['title' => ''], null)->shouldHaveKeyWithValue('title', null);
    }

    public function it_triggers_save_callbacks_with_the_context(QueryBuilder $queryBuilder): void
    {
        $context                                      = new \stdClass();
        $this->dca['fields']['title']['save_callback'] = [
            static fn (string $value, object $dc): string => $dc === $context ? $value . '!' : $value,
        ];

        $queryBuilder->setParameter('title', 'New!', 'string')->shouldBeCalled()->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['title' => 'New'], $context)->shouldHaveKeyWithValue('title', 'New!');
    }

    public function it_implodes_csv_values(QueryBuilder $queryBuilder): void
    {
        $this->dca['fields']['title']['eval'] = ['multiple' => true, 'csv' => ','];

        $queryBuilder->setParameter('title', 'a,b', 'string')->shouldBeCalled()->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['title' => ['a', 'b']], null)->shouldHaveKeyWithValue('title', 'a,b');
    }

    public function it_serializes_array_values(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->setParameter('title', 'a:1:{i:0;s:1:"a";}', 'string')
            ->shouldBeCalled()
            ->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['title' => ['a']], null);
    }

    public function it_throws_if_unique_value_already_exists(Connection $connection): void
    {
        $this->dca['fields']['title']['eval']['unique'] = true;

        $connection->fetchOne('SELECT COUNT(*) FROM tl_example WHERE title=? AND id!=?', ['New', 1])->willReturn(1);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('update', ['tl_example', 1, ['title' => 'New'], null]);
    }

    public function it_saves_unique_values(Connection $connection, QueryBuilder $queryBuilder): void
    {
        $this->dca['fields']['title']['eval']['unique'] = true;

        $connection->fetchOne('SELECT COUNT(*) FROM tl_example WHERE title=? AND id!=?', ['New', 1])->willReturn(0);
        $queryBuilder->executeStatement()->shouldBeCalled()->willReturn(1);

        $this->update('tl_example', 1, ['title' => 'New'], null);
    }

    public function it_triggers_onbeforesubmit_callbacks(QueryBuilder $queryBuilder): void
    {
        $this->dca['config']['onbeforesubmit_callback'] = [
            static fn (array $values): array => [...$values, 'sorting' => 64],
        ];

        $queryBuilder->setParameter('sorting', 64, 'integer')->shouldBeCalled()->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['title' => 'New'], null)->shouldHaveKeyWithValue('sorting', 64);
    }

    public function it_triggers_onsubmit_callbacks(): void
    {
        $called                                   = false;
        $this->dca['config']['onsubmit_callback'] = [
            static function () use (&$called): void {
                $called = true;
            },
        ];

        $this->update('tl_example', 1, ['title' => 'New'], null);

        if (! $called) {
            throw new \RuntimeException('onsubmit_callback was not called');
        }
    }

    public function it_invalidates_cache_tags_of_record_and_parent(CacheTagManager $cacheTagManager): void
    {
        $cacheTagManager->invalidateTags(['contao.db.tl_example.1', 'contao.db.tl_parent.3'])
            ->shouldBeCalled()
            ->willReturn($cacheTagManager);

        $this->update('tl_example', 1, ['title' => 'New'], null);
    }

    public function it_does_not_invalidate_cache_tags_if_nothing_changed(
        CacheTagManager $cacheTagManager,
        QueryBuilder $queryBuilder,
    ): void {
        $queryBuilder->executeStatement()->willReturn(0);
        $cacheTagManager->invalidateTags(Argument::any())->shouldNotBeCalled();

        $this->update('tl_example', 1, ['title' => 'New'], null);
    }

    public function it_combines_virtual_fields(
        VirtualFieldsHandler $virtualFieldsHandler,
        QueryBuilder $queryBuilder,
    ): void {
        $this->dca['fields']['color'] = ['exclude' => false, 'sql' => ['type' => 'string']];
        $this->dca['fields']['jsonData'] = ['sql' => ['type' => 'json']];

        $virtualFieldsHandler->expandFields(Argument::type('array'), 'tl_example')
            ->willReturn([...self::RECORD, 'color' => 'red', 'size' => 'xl']);

        $virtualFieldsHandler
            ->combineFields(
                Argument::that(
                    static fn (array $values): bool => $values['color'] === 'blue' && $values['size'] === 'xl',
                ),
                'tl_example',
            )
            ->shouldBeCalled()
            ->willReturn(['jsonData' => ['color' => 'blue', 'size' => 'xl'], 'tstamp' => 123]);

        $queryBuilder->setParameter('jsonData', ['color' => 'blue', 'size' => 'xl'], 'json')
            ->shouldBeCalled()
            ->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['color' => 'blue'], null);
    }

    public function it_resets_other_fallback_values_within_the_parent(QueryBuilder $queryBuilder): void
    {
        $this->dca['fields']['published']['exclude']          = false;
        $this->dca['fields']['published']['eval']['fallback'] = true;

        $queryBuilder->set('published', ':value')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('value', false, 'boolean')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->where('pid = :pid')->shouldBeCalled()->willReturn($queryBuilder);
        $queryBuilder->setParameter('pid', 3)->shouldBeCalled()->willReturn($queryBuilder);

        $this->update('tl_example', 1, ['published' => '1'], null);
    }
}
