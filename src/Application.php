<?php

namespace App;

use App\Bootstrap\Bootstrapper;
use App\Bootstrap\Kernel;
use App\Bootstrap\KernelInterface;
use App\DependencyInjection\Container;
use App\ServiceProviders\ServiceProvider;
use App\Utils\Config;
use Psr\Container\ContainerInterface;

class Application extends DependencyInjection\Container
{
    public string $basePath = '/';
    public string $appPath = '/src';
    public string $publicPath = '/public';

    public array $serviceProviders = [];

    public function __construct(string $basePath)
    {
        $this->setPaths($basePath);
        $this->registerBaseComponents();
        $this->registerCoreAliases();
    }

    private function setPaths(string $basePath)
    {
        $this->basePath = $basePath;
        $this->publicPath = $basePath . '/public';
        $this->appPath = $basePath . '/src';
    }

    private function registerBaseComponents()
    {
        // We register our application inside the container.
        // ToDo: replace with array syntax
        $this->set('app', $this);
        $this->set(self::class, $this);
        $this->set(Container::class, $this);
        $this->set(ContainerInterface::class, $this);
    }

    private function registerCoreAliases()
    {
        $aliases = [
            'config' => [Config::class]
        ];

        foreach ($aliases as $key => $alias) {
            foreach ($alias as $alia) {
                $this->singleton([$key], $alia);
            }
        }
    }

    public function registerService(ServiceProvider $service)
    {
        $this->set($service::class, $service);

        // logs the registered service provider
        $this->serviceProviders[] = $service::class;

        $service->register();
    }

    public function bindKernel()
    {
        $this->set([KernelInterface::class, 'kernel'], Kernel::class);

        return $this->make(KernelInterface::class);
    }

    /**
     * @param Bootstrapper[] $bootstrappers
     * @return void
     */
    public function bootstrap(array $bootstrappers)
    {
        foreach ($bootstrappers as $bootstrapper) {
            $bootstrapper = $this->make($bootstrapper);

            if (!$bootstrapper instanceof Bootstrapper) {
                continue;
            }

            $bootstrapper->bootstrap();
        }
    }
}