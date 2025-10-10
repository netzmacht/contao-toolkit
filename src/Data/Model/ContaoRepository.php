<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;
use Contao\Model\Collection;
use Netzmacht\Contao\Toolkit\Assertion\Assert;
use Override;

use function array_map;
use function call_user_func_array;
use function str_replace;

/**
 * @template T of Model
 * @implements Repository<T>
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ContaoRepository implements Repository
{
    use QueryProxy;

    /** @param class-string<T> $modelClass Model class. */
    public function __construct(private readonly string $modelClass)
    {
        Assert::that($modelClass)
            ->classExists()
            ->subclassOf(Model::class);
    }

    #[Override]
    public function getTableName(): string
    {
        return $this->call('getTable');
    }

    #[Override]
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    #[Override]
    public function find(int $modelId): Model|null
    {
        return $this->call('findByPK', [$modelId]);
    }

    /** {@inheritDoc} */
    #[Override]
    public function findBy(array $column, array $values, array $options = []): Collection|null
    {
        $column  = $this->addTablePrefix($column);
        $options = $this->addTablePrefixToOrder($options);

        return $this->call('findBy', [$column, $values, $options]);
    }

    /** {@inheritDoc} */
    #[Override]
    public function findOneBy(array $column, array $values, array $options = []): Model|null
    {
        $column  = $this->addTablePrefix($column);
        $options = $this->addTablePrefixToOrder($options);

        return $this->call('findOneBy', [$column, $values, $options]);
    }

    /** {@inheritDoc} */
    #[Override]
    public function findBySpecification(Specification $specification, array $options = []): Collection|Model|null
    {
        $column  = [];
        $values  = [];
        $options = $this->addTablePrefixToOrder($options);

        if ($specification instanceof QuerySpecification) {
            $specification->buildQueryWithOptions($column, $values, $options);
        } else {
            $specification->buildQuery($column, $values);
        }

        if ($column === []) {
            return $this->findAll($options);
        }

        $column = $this->addTablePrefix($column);

        return $this->findBy($column, $values, $options);
    }

    /** {@inheritDoc} */
    #[Override]
    public function findAll(array $options = []): Collection|null
    {
        $options = $this->addTablePrefixToOrder($options);

        return $this->call('findAll', [$options]);
    }

    /** {@inheritDoc} */
    #[Override]
    public function countBy(array $column, array $values): int
    {
        $column = $this->addTablePrefix($column);

        return $this->call('countBy', [$column, $values]);
    }

    #[Override]
    public function countBySpecification(Specification $specification): int
    {
        $column = [];
        $values = [];

        $specification->buildQuery($column, $values);

        if ($column === []) {
            return $this->countAll();
        }

        return $this->countBy($column, $values);
    }

    #[Override]
    public function countAll(): int
    {
        return $this->call('countAll');
    }

    #[Override]
    public function save(Model $model): void
    {
        $model->save();
    }

    #[Override]
    public function delete(Model $model): void
    {
        $model->delete();
    }

    /**
     * Make a call on the model.
     *
     * @param string      $method    Method name.
     * @param list<mixed> $arguments Arguments.
     */
    protected function call(string $method, array $arguments = []): mixed
    {
        return call_user_func_array([$this->modelClass, $method], $arguments);
    }

    /**
     * Replace placeholder for the table prefix.
     *
     * @param list<string> $column List of columns.
     *
     * @return list<string>
     */
    protected function addTablePrefix(array $column): array
    {
        return array_map(
            [$this, 'addTablePrefixToColumn'],
            $column,
        );
    }

    /**
     * Add table prefix to a column.
     *
     * @param string $column The column.
     */
    private function addTablePrefixToColumn(string $column): string
    {
        $tableName = $this->getTableName();
        $column    = str_replace(
            ['..', ' .', ',.', '>.', '<.', '=.', '(.'],
            [
                $tableName . '.',
                ' ' . $tableName . '.',
                ',' . $tableName . '.',
                '>' . $tableName . '.',
                '<' . $tableName . '.',
                '=' . $tableName . '.',
                '(' . $tableName . '.',
            ],
            $column,
        );

        if ($column[0] === '.') {
            $column = $tableName . $column;
        }

        return $column;
    }

    /**
     * Add table prefix to the order.
     *
     * @param array<string,mixed> $options Query options.
     *
     * @return array<string,mixed>
     */
    private function addTablePrefixToOrder(array $options): array
    {
        if (isset($options['order'])) {
            $options['order'] = $this->addTablePrefixToColumn($options['order']);
        }

        return $options;
    }
}
