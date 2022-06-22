<?php

namespace App\Entity;

use App\Database\Hydration\Strategies\StrategyInterface;
use Attribute;

#[Attribute]
class Hydrator
{
    private string $strategy;

    /**
     * @param string $strategy = StrategyInterfaceFQCN
     */
    public function __construct(string $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getStrategy(): string
    {
        return $this->strategy;
    }
}