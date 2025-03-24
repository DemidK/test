<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register the PermissionService as a singleton
        $this->app->singleton(PermissionService::class, function ($app) {
            return new PermissionService();
        });
    }

    public function boot(): void
    {
        // The schema search path should be handled by middleware,
        // not in the service provider boot method
        
        // No need to register class-based components as you're using Blade component files directly
    }
}