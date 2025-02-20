<?php

namespace Dtvmedia\LaravelLogTracer;

use Illuminate\Log\LogManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelLogTracerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-log-tracer')
            ->hasConfigFile();
    }

    public function boot(): void
    {
        $this->app->extend('log', function (LogManager $logger) {
            $monolog = $logger->getLogger();

            if (method_exists($monolog, 'pushProcessor')) {
                $monolog->pushProcessor(new LaravelLogTracer);
            }

            return $logger;
        });

        parent::boot();
    }
}
