<?php

namespace Tuijncode\LaravelVersion;

use Illuminate\Support\Facades\Route;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Config

        $this->publishes([
            __DIR__.'/config/laravel_version.php' => config_path('laravel_version.php'),
        ]);

        // Routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}
