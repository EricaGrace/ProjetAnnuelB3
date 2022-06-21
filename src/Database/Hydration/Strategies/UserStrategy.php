<?php

namespace App\Database\Hydration\Strategies;

use App\Repository\UserRepository;

class UserStrategy implements StrategyInterface
{

    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function hydrate($id)
    {
        return $id ? $this->repository->find($id) : null;
    }
}