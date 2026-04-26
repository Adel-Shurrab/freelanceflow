<?php

namespace App\Repositories;

use App\Contracts\Repositories\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function findById(int|string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findByIdOrFail(int|string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function findAll(): Collection
    {
        return $this->query()->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->refresh();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Allow repositories to access the underlying model query builder.
     */
    protected function query(): Builder
    {
        return $this->model->newQuery();
    }
}
