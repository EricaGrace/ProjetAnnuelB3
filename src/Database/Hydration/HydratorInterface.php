<?php

namespace App\Database\Hydration;

interface HydratorInterface
{
    public function hydrate(array $values, object $entity);
}