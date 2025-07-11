<?php

namespace App\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseRepository
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function get($options): Paginator
    {
        return $this->model::options($options)
            ->paginate(request('limit', 10));
    }

    public function find(int $id): Model
    {
        $find = $this->model::withAllRelations()->find($id);
        if (!$find) {
            throw new HttpException(404, 'Resource not found');
        }

        return $find;
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->find($id)->update($data);
    }

    public function delete(int $id): bool
    {
        $item = $this->model->find($id);

        if (!$item) {
            return false;
        }

        return (bool) $item->delete();
    }
}
