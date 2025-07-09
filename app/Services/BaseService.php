<?php

namespace App\Services;

abstract class BaseService
{
    public function __construct(protected $repository)
    {
        $this->repository = $repository;
    }

    public function get($options = null)
    {
        return $this->repository->get($options);
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}
