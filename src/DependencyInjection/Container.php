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
    private array $singletons = [];

    public function callClassMethod(string|object $objectOrClass, string $method, mixed ...$values)
    {
        $method = (new ReflectionClass($objectOrClass))->getMethod($method);
        $methodParameters = $method->getParameters();
        $method = $method->name;

        $arguments = $this->instanciateInstanciableParameters($methodParameters);

        $object = gettype($objectOrClass) === 'string' ? $this->make($objectOrClass) : $objectOrClass;
        return $object->$method(...array_merge($arguments, $values));
    }

    /**
     * @param ReflectionParameter[] $methodParameters
     * @return array
     */
    private function instanciateInstanciableParameters(array $methodParameters): array
    {
        $arguments = [];
        foreach ($methodParameters as $parameter) {
            if ($this->parameterIsAClass($parameter)) {
                $arguments[] = $this->make($parameter->getType()->getName());
            }
        }

        return $arguments;
    }

    private function parameterIsAClass(ReflectionParameter $parameter): bool
    {
        return !$this->parameterIsNotAClass($parameter);
    }

    private function parameterIsNotAClass(ReflectionParameter $parameter): bool
    {
        return (!$parameter->getType() || $parameter->getType()->isBuiltin());
    }

    public function make(mixed $id, mixed ...$arguments): ?object
    {
        try {
            $service = $this->get($id);
        } catch (ServiceNotFoundException) {
            $service = $id;
        }

        if (is_object($service)) {
            return $service;
        }

        try {
            $reflected = new ReflectionClass($service);
        } catch (ReflectionException) {
            return null;
        }

        if (!$reflected->isInstantiable()) {
            return null;
        }

        $reflectedConstructor = $reflected->getConstructor();
        $constructorParameters = $reflectedConstructor ? $reflectedConstructor->getParameters() : [];

        $parameters = $this->instanciateInstanciableParameters($constructorParameters);

        $instance = $reflected->newInstance(...array_merge($parameters, $arguments));

        if ($this->isASingleton($id)) {
            // we bind the freshly made instance to the container if it is not registered as a singleton
            $this->overrideKey($id, $instance);
        }
        return $instance;
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new ServiceNotFoundException($id);
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

    private function isASingleton(mixed $id): bool
    {
        return array_key_exists($id, $this->singletons);
    }

    private function overrideKey(mixed $id, object|string $service): void
    {
        $this->services[$id] = $service;
    }

    public function singleton(string|array $id, $service): void
    {
        $this->set($id, $service, true);
    }

    public function set(string|array $ids, $service, bool $singleton = false)
    {
        foreach ((array)$ids as $id) {
            if ($this->has($id)) {
                throw new InvalidArgumentException(sprintf('The "%s" service is already initialized, you cannot replace it.', $id));
            }

            $this->services[$id] = $service;

            if ($singleton) {
                $this->singletons[$id] = $service;
            }
        }
    }
}
