<?php

namespace Dtvmedia\LaravelLogTracer;

use Illuminate\Log\LogManager;
use Illuminate\Support\ServiceProvider;

class LaravelLogTracerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->bootConfigs();
        $this->bootLogExtension();
    }

    public function register(): void
    {
        $this->registerConfigs();
    }

    protected function bootLogExtension(): void
    {
        $this->app->extend('log', function (LogManager $logger) {
            $monolog = $logger->getLogger();

            if (method_exists($monolog, 'pushProcessor')) {
                $monolog->pushProcessor(new LaravelLogTracer);
            }

            return $logger;
        });
    }

    protected function bootConfigs(): void
    {
        if ($this->app->runningInConsole()) {
            $vendorConfig = __DIR__.'/../config/log-tracer.php';

            $this->publishes(
                paths: [
                    $vendorConfig => config_path('log-tracer.php'),
                ],
                groups: 'log-tracer-config'
            );
        }
    }

    protected function registerConfigs(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/log-tracer.php',
            'log-tracer'
        );
    }
}
