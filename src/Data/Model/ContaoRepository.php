<?php

/**
 * Contao toolkit.
 *
 * @package    contao-toolkit
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2015-2017 netzmacht David Molineus.
 * @license    LGPL-3.0 https://github.com/netzmacht/contao-toolkit/blob/master/LICENSE
 * @filesource
 */

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;
use Netzmacht\Contao\Toolkit\Assertion\Assert;

/**
 * Class ContaoRepository.
 *
 * @package Netzmacht\Contao\Toolkit\Data\Model
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class ContaoRepository implements Repository
{
    use QueryProxy;

    /**
     * The model class.
     *
     * @var string
     */
    private $modelClass;

    /**
     * ContaoRepository constructor.
     *
     * @param string $modelClass Model class.
     */
    public function __construct(string $modelClass)
    {
        Assert::that($modelClass)
            ->classExists()
            ->subclassOf(Model::class);

        $this->modelClass = $modelClass;
    }

    /**
     * {@inheritDoc}
     */
    public function getTableName(): string
    {
        return $this->call('getTable');
    }

    /**
     * {@inheritDoc}
     */
    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     * {@inheritDoc}
     */
    public function find(int $modelId)
    {
        return $this->call('findByPK', [$modelId]);
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $column, array $values, array $options = [])
    {
        $column  = $this->addTablePrefix($column);
        $options = $this->addTablePrefixToOrder($options);

        return $this->call('findBy', [$column, $values, $options]);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $column, array $values, array $options = [])
    {
        $column  = $this->addTablePrefix($column);
        $options = $this->addTablePrefixToOrder($options);

        return $this->call('findOneBy', [$column, $values, $options]);
    }

    /**
     * {@inheritDoc}
     */
    public function findBySpecification(Specification $specification, array $options = [])
    {
        $column = [];
        $values = [];

        $specification->buildQuery($column, $values);

        return $this->findBy($column, $values, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll(array $options = [])
    {
        return $this->call('findAll', [$options]);
    }

    /**
     * {@inheritDoc}
     */
    public function countBy(array $column, array $values): int
    {
        $column = $this->addTablePrefix($column);

        return $this->call('countBy', [$column, $values]);
    }

    /**
     * {@inheritDoc}
     */
    public function countBySpecification(Specification $specification): int
    {
        $column = [];
        $values = [];

        $specification->buildQuery($column, $values);

        return $this->countBy($column, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function countAll(): int
    {
        return $this->call('countAll');
    }

    /**
     * {@inheritDoc}
     */
    public function save(Model $model)
    {
        $model->save();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Model $model)
    {
        $model->delete();
    }

    /**
     * Make a call on the model.
     *
     * @param string $method    Method name.
     * @param array  $arguments Arguments.
     *
     * @return mixed
     */
    protected function call(string $method, array $arguments = [])
    {
        return call_user_func_array([$this->modelClass, $method], $arguments);
    }

    /**
     * Replace placeholder for the table prefix.
     *
     * @param array $column List of columns.
     *
     * @return array
     */
    protected function addTablePrefix(array $column): array
    {
        return array_map(
            [$this, 'addTablePrefixToColumn'],
            $column
        );
    }

    /**
     * Add table prefix to a column.
     *
     * @param string $column The column.
     *
     * @return string
     */
    private function addTablePrefixToColumn(string $column): string
    {
        $tableName = $this->getTableName();
        $column    = str_replace(
            ['..', ' .', ',.', '>.', '<.', '=.'],
            [
                $tableName . '.',
                ' ' . $tableName . '.',
                ',' . $tableName . '.',
                '>' . $tableName . '.',
                '<' . $tableName . '.',
                '=' . $tableName . '.',
            ],
            $column
        );

        if ($column[0] === '.') {
            $column = $tableName . $column;
        }

        return $column;
    }

    /**
     * Add table prefix to the order.
     *
     * @param array $options Query options.
     *
     * @return array
     */
    private function addTablePrefixToOrder(array $options): array
    {
        if (isset($options['order'])) {
            $options['order'] = $this->addTablePrefixToColumn($options['order']);
        }

        return $options;
    }
}
