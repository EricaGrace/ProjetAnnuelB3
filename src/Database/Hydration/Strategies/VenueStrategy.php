<?php

namespace App\Database\Hydration\Strategies;

use App\Repository\VenueRepository;

class VenueStrategy implements StrategyInterface
{

    private VenueRepository $repository;

    public function __construct(VenueRepository $repository)
    {
        $this->repository = $repository;
    }

    public function hydrate($id)
    {
        return $id ? $this->repository->find($id) : null;
    }
}