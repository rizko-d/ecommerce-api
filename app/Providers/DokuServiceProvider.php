<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\DokuService;

class DokuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(DokuService::class, function ($app) {
            return new DokuService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
