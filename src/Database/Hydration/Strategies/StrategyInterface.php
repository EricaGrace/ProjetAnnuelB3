<?php

namespace App\Database\Hydration\Strategies;

interface StrategyInterface
{
    public function hydrate($value);
}