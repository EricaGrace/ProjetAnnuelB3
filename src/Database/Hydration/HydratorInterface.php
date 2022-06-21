<?php

namespace App\Database\Hydration;

use App\Entity\Entity;

interface HydratorInterface
{
    public function hydrate(array $values, Entity $object): Entity;
}