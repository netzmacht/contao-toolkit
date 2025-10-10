<?php

declare(strict_types=1);

namespace Netzmacht\Contao\Toolkit\Data\Model;

use Contao\Model;
use Contao\Model\Collection;

/**
 * Interface Repository describes a very base repository.
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
     * @psalm-return T|null
     */
    public function find(int $modelId): Model|null;

    /**
     * Find records by various criteria.
     *
     * @param list<string>        $column  Column criteria.
     * @param list<mixed>         $values  Column values.
     * @param array<string,mixed> $options Options.
     *
     * @return Collection<T>|null
     */
    public function findBy(array $column, array $values, array $options = []): Collection|null;

    /**
     * Find single record by various criteria.
     *
     * @param list<string>        $column  Column criteria.
     * @param list<mixed>         $values  Column values.
     * @param array<string,mixed> $options Options.
     *
     * @psalm-return T|null
     */
    public function findOneBy(array $column, array $values, array $options = []): Model|null;

    /**
     * Find records by specification.
     *
     * @param Specification       $specification Specification.
     * @param array<string,mixed> $options       Options.
     *
     * @psalm-return Collection<T>|T|null
     */
    public function findBySpecification(Specification $specification, array $options = []): Collection|Model|null;

    /**
     * Find all records.
     *
     * @param array<string,mixed> $options Query options.
     *
     * @psalm-return Collection<T>|null
     */
    public function findAll(array $options = []): Collection|null;

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
     */
    public function save(Model $model): void;

    /**
     * Delete a model.
     *
     * @param       Model $model Model being deleted.
     * @psalm-param T     $model
     */
    public function delete(Model $model): void;
}
