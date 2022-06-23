<?php

namespace App\Database\Hydration\Strategies;

use App\Repository\RoleRepository;

class RoleStrategy implements StrategyInterface
{

    private RoleRepository $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function hydrate($id)
    {
        return $id ? $this->repository->find($id) : null;
    }
}