<?php

namespace App\Database\Hydration\Strategies;

use App\Repository\EventRepository;

class EventStrategy implements StrategyInterface
{

    private EventRepository $repository;

    public function __construct(EventRepository $repository)
    {
        $this->repository = $repository;
    }

    public function hydrate($id)
    {
        return $id ? $this->repository->find($id) : null;
    }
}