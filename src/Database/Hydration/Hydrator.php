<?php

namespace App\Database\Hydration;

use App\Application;
use App\Entity\Entity;
use App\Entity\Hydrator as HydratorAttribute;
use ReflectionClass;

class Hydrator implements HydratorInterface
{
//    TODO: replace $reflected with $this->reflected
    private ReflectionClass $reflected;
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function hydrate(array $values, Entity $object): Entity
    {
        $reflected = new ReflectionClass($object);

        foreach ($values as $key => $value) {

            $property = $reflected->getProperty($key);
            $guessedSetter = 'set' . ucfirst($property->getName());

            if (!empty($attributes = $property->getAttributes(HydratorAttribute::class))) {
                $strategy = $attributes[0]->newInstance()->getStrategy();
                $value = $this->app->make($strategy)->hydrate($value);
            }

            if ($reflected->hasMethod($guessedSetter)) {
                call_user_func([$object, $guessedSetter], $value);
            } else {
                $property->setValue($object, $value);
            }
        }

        return $object;
    }
}