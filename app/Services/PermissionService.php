<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;

class PermissionService
{
    /**
     * Check if the current user has a specific permission
     */
    public function userCan($permission)
    {
        if (!Auth::check()) {
            return false;
        }
        
        $user = Auth::user();
        
        // Superusers bypass all permission checks.
        if ($user->isSuperuser()) {
            return true;
        }
        
        // For non-superusers, use cache for better performance.
        // The cache stores all allowed route names for the user.
        $cacheKey = "user_{$user->id}_permissions";
        
        $userPermissions = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($user) {
            $permissions = [];
            foreach ($user->roles as $role) {
                $permissions = array_merge($permissions, $role->permissions ?? []);
            }
            return array_unique($permissions);
        });

        // Check for a direct match or a wildcard match.
        if (in_array('*', $userPermissions) || in_array($permission, $userPermissions)) {
            return true;
        }

        foreach ($userPermissions as $userPermission) {
            if (Str::is($userPermission, $permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if the current user has any of the specified permissions
     */
    public function userCanAny(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->userCan($permission)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if the current user has all of the specified permissions
     */
    public function userCanAll(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->userCan($permission)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if a specific action is allowed based on resource and action
     * 
     * @param string $action The action (view, create, edit, delete)
     * @param string $resource The resource (users, roles, permissions, etc.)
     * @return bool
     */
    public function can($action, $resource)
    {
        $permissionSlug = "{$action}_{$resource}";
        return $this->userCan($permissionSlug);
    }
    
    /**
     * Clear permission cache for a user
     */
    public function clearCache($userId = null)
    {
        if ($userId) {
            Cache::forget("user_{$userId}_permissions");
            Cache::forget("user_{$userId}_is_superuser");
        } else {
            // If no user ID is provided, this could be a signal to clear for all users.
            // This can be slow, use with caution.
            Log::warning('Clearing permission cache for all users.');
            User::pluck('id')->each(function ($id) {
                $this->clearCache($id);
            });
        }
    }

    /**
     * Gets all named routes and groups them by a resource name.
     * The resource is determined by the first part of the route name (before the first dot).
     */
    public function getGroupedRoutes(): array
    {
        $routes = Route::getRoutes()->getRoutes();
        $groupedRoutes = [];

        foreach ($routes as $route) {
            $name = $route->getName();
            // Filter out system routes
            if ($name && !Str::startsWith($name, ['ignition.', 'sanctum.', 'livewire.'])) {
                $parts = explode('.', $name);
                $resource = $parts[0] ?? 'general';
                $action = $parts[1] ?? 'index';

                $resourceName = Str::title(str_replace(['_', '-'], ' ', $resource));

                if (!isset($groupedRoutes[$resourceName])) {
                    $groupedRoutes[$resourceName] = [];
                }

                $groupedRoutes[$resourceName][] = ['name' => $name, 'action' => $action];
            }
        }
        ksort($groupedRoutes);
        return $groupedRoutes;
    }
}