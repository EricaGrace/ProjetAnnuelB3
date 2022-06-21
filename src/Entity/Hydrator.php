<?php

namespace App\Entity;

use Attribute;

#[Attribute]
class Hydrator
{
    private string $strategy;

    public function __construct(string $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getStrategy(): string
    {
        return $this->strategy;
    }
}