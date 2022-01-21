<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;
use Contao\Model\Collection;

/**
 * Interface Repository describes a very base repository.
 *
 * phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
 *
 * @psalm-template T of Model
 */
interface Repository
{
    /**
     * Get the table name which is handled by the repository.
     */
    public function getTableName(): string;

    /**
     * Get the model class which is handled by the repository.
     *
     * @return class-string<T>
     */
    public function getModelClass(): string;

    /**
     * Find a model id it's id. Returns null if not found.
     *
     * @param int $modelId Model id.
     *
     * @return Model|null
     * @psalm-return T|null
     */
    public function find(int $modelId);

    /**
     * Find records by various criteria.
     *
     * @param list<string>        $column  Column criteria.
     * @param list<mixed>         $values  Column values.
     * @param array<string,mixed> $options Options.
     *
     * @return Collection|null
     */
    public function findBy(array $column, array $values, array $options = []);

    /**
     * Find single record by various criteria.
     *
     * @param list<string>        $column  Column criteria.
     * @param list<mixed>         $values  Column values.
     * @param array<string,mixed> $options Options.
     *
     * @return Model|null
     * @psalm-return T|null
     */
    public function findOneBy(array $column, array $values, array $options = []);

    /**
     * Find records by specification.
     *
     * @param Specification       $specification Specification.
     * @param array<string,mixed> $options       Options.
     *
     * @return Collection|Model|null
     * @psalm-return Collection|T|null
     */
    public function findBySpecification(Specification $specification, array $options = []);

    /**
     * Find all records.
     *
     * @param array<string,mixed> $options Query options.
     *
     * @return Collection|Model|null
     * @psalm-return Collection|T|null
     */
    public function findAll(array $options = []);

    /**
     * Count by various criteria.
     *
     * @param list<string> $column Column criteria.
     * @param list<mixed>  $values Column values.
     */
    public function countBy(array $column, array $values): int;

    /**
     * Count by specification.
     *
     * @param Specification $specification Specification.
     */
    public function countBySpecification(Specification $specification): int;

    /**
     * The total number of rows.
     */
    public function countAll(): int;

    /**
     * Save a model.
     *
     * @param       Model $model Model being saved.
     * @psalm-param T     $model
     *
     * @return void
     */
    public function save(Model $model);

    /**
     * Delete a model.
     *
     * @param       Model $model Model being deleted.
     * @psalm-param T     $model
     *
     * @return void
     */
    public function delete(Model $model);
}
