<?php

namespace App\Database\Hydration;

use App\Entity\Hydrator as HydratorAttribute;
use ReflectionClass;

class Hydrator implements HydratorInterface
{
//    TODO: replace $reflected with $this->reflected
    private ReflectionClass $reflected;

    public function hydrate(array $values, object $entity)
    {
        $reflected = new ReflectionClass($entity);

        foreach ($values as $key => $value) {

            $property = $reflected->getProperty($key);
            $guessedSetter = 'set' . ucfirst($property->getName());

            if (!empty($attributes = $property->getAttributes(HydratorAttribute::class))) {
                $strategy = $attributes[0]->newInstance()->getStrategy();
                $value = (new $strategy())->hydrate($value);
            }

            call_user_func([$entity, $guessedSetter], $value);
        }

        return $entity;
    }
}