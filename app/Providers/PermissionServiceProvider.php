<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Skip this on migrations
        if (!Schema::hasTable('permissions')) {
            return;
        }
        
        // Register default guest permission
        $this->registerGuestPermission();
        
        // Register permissions based on routes
        $this->registerRoutePermissions();
    }
    
    /**
     * Register the guest permission
     */
    protected function registerGuestPermission(): void
    {
        $permissionService = app(PermissionService::class);
        
        $permissionService->registerPermission(
            'Guest Access',
            'Public access to non-authenticated areas',
            'guest',
            null,
            null
        );
    }
    
    /**
     * Register permissions based on routes with permission middleware
     */
    protected function registerRoutePermissions(): void
    {
        $permissionService = app(PermissionService::class);
        $routes = Route::getRoutes();
        
        foreach ($routes as $route) {
            $middlewares = $route->gatherMiddleware();
            
            foreach ($middlewares as $middleware) {
                if (strpos($middleware, 'permission:') === 0) {
                    // Extract permission name
                    $permission = substr($middleware, 11);
                    
                    // Skip guest permission
                    if ($permission === 'guest') {
                        continue;
                    }
                    
                    // Figure out resource and action from permission name
                    if (preg_match('/^(view|create|edit|delete|manage|export|import)_(.+)$/', $permission, $matches)) {
                        $action = $matches[1];
                        $resource = $matches[2];
                        
                        // Create a nice readable name
                        $name = ucfirst($action) . ' ' . ucwords(str_replace('_', ' ', $resource));
                        
                        // Register the permission if it doesn't exist
                        $permissionService->registerPermission(
                            $name,
                            "Can {$action} {$resource}",
                            'route',
                            $resource,
                            $action
                        );
                    } else {
                        // For permissions that don't follow the standard pattern
                        $name = ucwords(str_replace('_', ' ', $permission));
                        
                        $permissionService->registerPermission(
                            $name,
                            "Custom permission: {$permission}",
                            'custom'
                        );
                    }
                }
            }
        }
    }
}