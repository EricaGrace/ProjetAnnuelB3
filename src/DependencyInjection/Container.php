<?php

namespace App\DependencyInjection;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class Container implements ContainerInterface
{
    private array $services = [];

    public function set(string|array $ids, $service)
    {
        foreach ((array)$ids as $id) {
            if ($this->has($id)) {
                throw new InvalidArgumentException(sprintf('The "%s" service is already initialized, you cannot replace it.', $id));
            }

            $this->services[$id] = $service;
        }
    }

    public function has(string $id)
    {
        return array_key_exists($id, $this->services);
    }

    public function make(mixed $id, mixed ...$arguments): ?object
    {

        try {
            $service = $this->get($id);
        } catch (ServiceNotFoundException) {
            // Kernel::class
            $service = $id;
        }

        if (is_object($service)) {
            return $service;
        }

        try {
            $reflected = new ReflectionClass($service);
        } catch (ReflectionException $e) {
            return null;
        }

        if (!$reflected->isInstantiable()) {
            return null;
        }

        $reflectedConstructor = $reflected->getConstructor();
        $constructorParameters = $reflectedConstructor ? $reflectedConstructor->getParameters() : [];

        $parameters = [];
        foreach ($constructorParameters as $parameter) {
            if ($this->parameterIsNotAClass($parameter)) {
                continue;
            }

            $class = $parameter->getType()->getName();

            if (!$this->has($class)) {
                $param = $this->make($class);
                $parameters[] = $param;
            } else {
                $parameters[] = $this->get($class);
            }
        }

        $instance = $reflected->newInstance(...array_merge($parameters, $arguments));

        // we bind the freshly made instance to the container
        $this->overrideKey($service, $instance);
        return $instance;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new ServiceNotFoundException($id);
        }

        return $this->services[$id];
    }

    private function parameterIsNotAClass(ReflectionParameter $parameter): bool
    {
        return (!$parameter->getType() || $parameter->getType()->isBuiltin());
    }

    private function overrideKey(mixed $id, object $service): void
    {
        $this->services[$id] = $service;
    }

}
