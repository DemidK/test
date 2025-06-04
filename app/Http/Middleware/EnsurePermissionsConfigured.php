<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class EnsurePermissionsConfigured
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        
        // Skip check for certain controllers or actions
        if ($this->shouldSkipCheck($route)) {
            return $next($request);
        }
        
        // If the route doesn't have a permission middleware, log a warning
        if (!$this->routeHasPermissionMiddleware($route)) {
            Log::warning("Route missing permission configuration: " . $route->uri() . " [" . implode(',', $route->methods()) . "]");
            
            // In production, we'll still let the request through but log it
            // In development, we might want to throw an exception to catch these issues early
            if (app()->environment('local', 'development', 'testing')) {
                // In development, block routes without permissions
                abort(500, 'Route missing permission configuration. Check logs for details.');
            }
        }
        
        return $next($request);
    }
    
    /**
     * Check if a route has a permission middleware
     */
    private function routeHasPermissionMiddleware($route)
    {
        $middlewares = $route->gatherMiddleware();
        
        foreach ($middlewares as $middleware) {
            if (strpos($middleware, 'permission:') === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Determine if the permission check should be skipped for this route
     */
    private function shouldSkipCheck($route)
    {
        // List of controllers or actions to skip permission checks for
        $skippedControllers = [
            'LoginController',
            'RegisterController',
            'ForgotPasswordController',
            'ResetPasswordController',
            'VerificationController',
        ];
        
        // Check controller name
        $controller = $route->getController();
        if ($controller) {
            $controllerClass = get_class($controller);
            foreach ($skippedControllers as $skippedController) {
                if (strpos($controllerClass, $skippedController) !== false) {
                    return true;
                }
            }
        }
        
        // Check explicit permission bypass in route action
        $action = $route->getAction();
        if (isset($action['bypass_permission_check']) && $action['bypass_permission_check'] === true) {
            return true;
        }
        
        return false;
    }
}