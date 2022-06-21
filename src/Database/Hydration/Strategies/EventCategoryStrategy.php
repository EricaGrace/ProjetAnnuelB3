<?php

namespace App\Database\Hydration\Strategies;

use App\Repository\EventCategoryRepository;

class EventCategoryStrategy implements StrategyInterface
{

    private EventCategoryRepository $repository;

    public function __construct(EventCategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function hydrate($id)
    {
        return $id ? $this->repository->find($id) : null;
    }
}